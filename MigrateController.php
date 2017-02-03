<?php

namespace console\controllers;

use core\di\Containering;
use core\di\ServiceLocating;
use dekey\di\mixins\ContainerAccess;
use dekey\di\mixins\ServiceLocatorAccess;
use yii\helpers\Console;

/**
 * MigrateController class extending {@link \yii\console\controllers\MigrateController}.
 * This class allows to store and use migration files in sub-directories
 *
 * Structure of sub-directories can be configured by property {@property subDirectoriesStructure}
 *
 * In this parameter you can use these variables:
 * {year} - current year (eg. 2015)
 * {month} - current month (January, February,.. December)
 * {digital_month} - current month as digit (01, 02,... 12)
 * {day} - current day of month (01, 02,...)
 *
 * For example:
 * <pre>
 * 'migrate' => [
 *        'class' => 'extensions\local\console\MigrateCommand',
 *        'migrationTable' => '_db_migration',
 *        'templateFile' => 'application.extensions.local.templates.migrations.template',
 *        'subDirectoriesStructure' => '{year}/{month}'
 * ],
 * </pre>
 *
 * @param string $subDirectoriesStructure public link for the {@link $_subDirectoriesStructure}
 *
 * @author Alexandr Stiopkin <sani@quartsoft.com>
 */
class MigrateController extends \yii\console\controllers\MigrateController {
    use ServiceLocatorAccess;
    use ContainerAccess;
    /**
     * @inheritdoc
     */
    public $templateFile = '@console/views/migration.php';
    /**
     * @var string scheme of directory structure
     */
    protected $_subDirectoriesStructure;
    /**
     * @var array List of variables that can be used in sub-directories scheme
     */
    private $pathTemplateVariables = [
        '{year}',
        '{month}',
        '{digital_month}',
        '{day}',
    ];

    /**
     * Upgrades the application by applying new migrations.
     * For example,
     *
     * ```
     * yii migrate     # apply all new migrations
     * yii migrate 3   # apply the first 3 new migrations
     * ```
     *
     * @param integer $limit the number of new migrations to be applied. If 0, it means
     * applying all available new migrations.
     *
     * @return integer the status of the action execution. 0 means normal, other values mean abnormal.
     */
    public function actionUp($limit = 0)
    {
        $migrations = $this->getNewMigrations();
        if (empty($migrations)) {
            $this->stdout("No new migrations found. Your system is up-to-date.\n", Console::FG_GREEN);

            return self::EXIT_CODE_NORMAL;
        }

        $total = count($migrations);
        $limit = (int) $limit;
        if ($limit > 0) {
            $migrations = array_slice($migrations, 0, $limit);
        }

        $n = count($migrations);
        if ($n === $total) {
            $this->stdout("Total $n new " . ($n === 1 ? 'migration' : 'migrations') . " to be applied:\n", Console::FG_YELLOW);
        } else {
            $this->stdout("Total $n out of $total new " . ($total === 1 ? 'migration' : 'migrations') . " to be applied:\n", Console::FG_YELLOW);
        }

        foreach ($migrations as $migration) {
            $this->stdout("\t{$migration['class']}\n");
        }
        $this->stdout("\n");

        $applied = 0;
        if ($this->confirm('Apply the above ' . ($n === 1 ? 'migration' : 'migrations') . '?')) {
            foreach ($migrations as $migration) {
                if (!class_exists($migration['class'])) {
                }
                if (!$this->migrateUp($migration)) {
                    $this->stdout("\n$applied from $n " . ($applied === 1 ? 'migration was' : 'migrations were') ." applied.\n", Console::FG_RED);
                    $this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", Console::FG_RED);

                    return self::EXIT_CODE_ERROR;
                }
                $applied++;
            }

            $this->stdout("\n$n " . ($n === 1 ? 'migration was' : 'migrations were') ." applied.\n", Console::FG_GREEN);
            $this->stdout("\nMigrated up successfully.\n", Console::FG_GREEN);
        }
    }

    /**
     * Downgrades the application by reverting old migrations.
     * For example,
     *
     * ```
     * yii migrate/down     # revert the last migration
     * yii migrate/down 3   # revert the last 3 migrations
     * yii migrate/down all # revert all migrations
     * ```
     *
     * @param integer $limit the number of migrations to be reverted. Defaults to 1,
     * meaning the last applied migration will be reverted.
     * @throws Exception if the number of the steps specified is less than 1.
     *
     * @return integer the status of the action execution. 0 means normal, other values mean abnormal.
     */
    public function actionDown($limit = 1)
    {
        if ($limit === 'all') {
            $limit = null;
        } else {
            $limit = (int) $limit;
            if ($limit < 1) {
                throw new Exception('The step argument must be greater than 0.');
            }
        }

        $migrations = $this->getMigrationHistory($limit);

        if (empty($migrations)) {
            $this->stdout("No migration has been done before.\n", Console::FG_YELLOW);

            return self::EXIT_CODE_NORMAL;
        }

        $migrations = array_keys($migrations);

        $n = count($migrations);
        $this->stdout("Total $n " . ($n === 1 ? 'migration' : 'migrations') . " to be reverted:\n", Console::FG_YELLOW);
        foreach ($migrations as $migration) {
            $this->stdout("\t$migration\n");
        }
        $this->stdout("\n");

        $reverted = 0;
        if ($this->confirm('Revert the above ' . ($n === 1 ? 'migration' : 'migrations') . '?')) {
            foreach ($migrations as $migration) {
                if (!$this->migrateDown($migration)) {
                    $this->stdout("\n$reverted from $n " . ($reverted === 1 ? 'migration was' : 'migrations were') ." reverted.\n", Console::FG_RED);
                    $this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", Console::FG_RED);

                    return self::EXIT_CODE_ERROR;
                }
                $reverted++;
            }
            $this->stdout("\n$n " . ($n === 1 ? 'migration was' : 'migrations were') ." reverted.\n", Console::FG_GREEN);
            $this->stdout("\nMigrated down successfully.\n", Console::FG_GREEN);
        }
    }

    /**
     * @inheritdoc
     */
    public function actionCreate($name) {
        $this->prepareDirectoryForNewMigration();

        parent::actionCreate($name);
    }

    protected function prepareDirectoryForNewMigration() {
        $this->migrationPath = $this->getCorrectPath();

        if (!file_exists($this->migrationPath) || !is_dir($this->migrationPath)) {
            mkdir($this->migrationPath, 0777, true);
        }
    }

    /**
     * Reorganize existing files according to subdirectory scheme
     */
    public function actionReorganize() {
        $this->moveFilesToBaseFolder();
        $this->deleteSubFolders($this->migrationPath);

        $dir = new \RecursiveDirectoryIterator($this->migrationPath);
        foreach (new \RecursiveIteratorIterator($dir) as $file) {
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }

            list($year, $month) = $this->getMigrationYearAndMonthCreated($file);
            $newPath = $this->getCorrectPath(\DateTime::createFromFormat('Y-m-d', $year . '-' . $month . '-01'));

            if (!file_exists($newPath) || !is_dir($newPath)) {
                mkdir($newPath, 0777, true);
            }
            rename($file->getRealPath(), $newPath . DIRECTORY_SEPARATOR . $file->getBasename());
        }
        $this->stdout("\nMigration files have been reorganized\n\n", Console::FG_GREEN);
    }

    public function initSubDirectoriesStructure() {
        $this->_subDirectoriesStructure = '{year}/{digital_month}';
    }

    /**
     * @param string $baseDir
     * @param bool|false $removeDir
     */
    protected function deleteSubFolders($baseDir, $removeDir = false) {
        $files = array_diff(scandir($baseDir), ['.', '..']);
        foreach ($files as $file) {
            if (is_dir("$baseDir/$file")) {
                $this->deleteSubFolders("$baseDir/$file", true);
            }
        }

        if ($removeDir) {
            rmdir($baseDir);
        }
    }

    /**
     * @inheritdoc
     * @override to support custom locations of migrations based on dates.
     */
    protected function createMigration($class) {
        $file = $this->recursiveFileSearch('/' . $class . '/');
        require_once($file);
        $migration = \Yii::createObject($class, [['db' => $this->db]]);
        $migration->db = $this->db;
        return $migration;
    }

    protected function moveFilesToBaseFolder() {
        $dir = new \RecursiveDirectoryIterator($this->migrationPath);
        foreach (new \RecursiveIteratorIterator($dir) as $file) {
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            $newDisposition = $this->migrationPath . DIRECTORY_SEPARATOR . $file->getFilename();
            rename($file->getRealPath(), $newDisposition);
        }
    }

    /**
     * @param string $pattern
     * @return null|string
     */
    protected function recursiveFileSearch($pattern) {
        $dir = new \RecursiveDirectoryIterator($this->migrationPath);
        $iteration = new \RecursiveIteratorIterator($dir);
        $files = new \RegexIterator($iteration, $pattern, \RegexIterator::GET_MATCH);

        $result = null;
        foreach ($files as $path => $file) {
            $result = $path;
            break;
        }
        return $result;
    }

    // GETTERS/SETTERS
    /**
     * Build full path according to path template
     *
     * @param DateTime|null $migrationDate It's used to replace template variables in sub-directory scheme. Default = null  - it means today
     */
    public function getCorrectPath(\DateTime $migrationDate = null) {
        if ($migrationDate === null) {
            $migrationDate = new \DateTime();
        }

        $replace = [
            $migrationDate->format('Y'),
            $migrationDate->format('F'),
            $migrationDate->format('m'),
            $migrationDate->format('d'),
        ];

        return $this->migrationPath . DIRECTORY_SEPARATOR . str_replace($this->pathTemplateVariables, $replace, $this->subDirectoriesStructure);
    }

    /**
     * @param \SplFileObject $file
     * @return array
     */
    public function getMigrationYearAndMonthCreated($file) {
        $result = [];

        $baseName = $file->getBasename();
        $result[] = '20' . substr($baseName, 1, 2);
        $result[] = substr($baseName, 3, 2);

        return $result;
    }

    /**
     * @return mixed
     */
    public function getSubDirectoriesStructure() {
        if (empty($this->_subDirectoriesStructure)) {
            $this->initSubDirectoriesStructure();
        }
        return $this->_subDirectoriesStructure;
    }

    /**
     * @param mixed $subDirectoriesStructure
     */
    public function setSubDirectoriesStructure($subDirectoriesStructure) {
        if (is_string($subDirectoriesStructure)) {
            $this->_subDirectoriesStructure = $subDirectoriesStructure;
        }
    }

    /**
     * @return array
     */
    protected function getNewMigrations() {
        /*$applied = [];
        foreach ($this->getMigrationHistory(null) as $version => $time) {
            $applied[substr($version, 1, 13)] = true;
        }*/

        /*$migrations = [];

        sort($migrations);
        return $migrations;*/
        $applied = [];
        foreach ($this->getMigrationHistory(null) as $class => $time) {
            $applied[trim($class, '\\')] = true;
        }

        $migrationPaths = [];
        if (empty($this->migrationNamespaces) && !empty($this->migrationPath)) {
            $migrationPaths[''] = $this->migrationPath;
        }
        foreach ($this->migrationNamespaces as $namespace) {
            $migrationPaths[$namespace] = $this->buildNamespacePath($namespace);
        }

        $migrations = [];
        foreach ($migrationPaths as $namespace => $migrationPath) {
            if (!file_exists($migrationPath)) {
                continue;
            }
            $dir = new \RecursiveDirectoryIterator($migrationPath);
            foreach (new \RecursiveIteratorIterator($dir) as $file) {
                $fileName = $file->getFilename();
                if ($fileName === '.' || $fileName === '..') {
                    continue;
                }

                $path = $file->getRealPath();
                if (preg_match('/^(m(\d{6}_\d{6})_(.*?))\.php$/', $fileName, $matches) && is_file($path) && !isset($applied[$matches[2]])) {
                    $fileName = $matches[1];
                    $class = $matches[3];
                    if (!empty($namespace)) {
                        $class = $namespace . '\\' . $class;
                    }
                    $time = str_replace('_', '', $matches[2]);
                    if (!isset($applied[$class])) {
                        $migrations[$time . '\\' . $class] = [
                            'class' => $class,
                            'namespace' => $namespace,
                            'fileName' => $fileName,
                        ];
                    }
                }
            }
        }
        ksort($migrations);

        return array_values($migrations);
    }

    /**
     * Returns the file path matching the give namespace.
     * @param string $namespace namespace.
     * @return string file path.
     * @since 2.0.10
     */
    private function buildNamespacePath($namespace)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, \Yii::getAlias('@' . str_replace('\\', '/', $namespace)));
    }
}