<?php

namespace PHPKitchen\Domain\Generator\Domain;

use PHPKitchen\Domain\Base\Entity;
use PHPKitchen\Domain\DB\EntitiesRepository;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;

Yii::$app->cache->flush();

/**
 * Class Generator
 *
 * @package PHPKitchen\Domain\Generator\Domain
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class Generator extends \yii\gii\generators\model\Generator {
    public $domainName;
    public $moduleName;
    public $domainPath = '@runtime/domains';
    public $ns = 'Module\\Domain\\Model';
    public $generateQuery = true;
    public $recordBaseClass = ActiveRecord::class;
    public $queryBaseClass = ActiveQuery::class;
    public $entityBaseClass = Entity::class;
    public $repositoryBaseClass = EntitiesRepository::class;

    /**
     * @inheritdoc
     */
    public function getName(): string {
        return 'Domain Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string {
        return 'This generator generates an domain for the specified database table.';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array {
        return [
            [['template'], 'required', 'message' => 'A code template must be selected.'],
            [['template'], 'validateTemplate'],
            [
                [
                    'db',
                    'ns',
                    'tableName',
                    'domainName',
                    'moduleName',
                    'recordBaseClass',
                    'queryBaseClass',
                    'entityBaseClass',
                    'repositoryBaseClass',
                ],
                'filter',
                'filter' => 'trim',
            ],
            [
                ['ns'],
                'filter',
                'filter' => function ($value) {
                    return trim($value, '\\');
                },
            ],
            [
                [
                    'db',
                    'ns',
                    'tableName',
                    'domainName',
                    'moduleName',
                    'recordBaseClass',
                    'queryBaseClass',
                    'entityBaseClass',
                    'repositoryBaseClass',
                ],
                'required',
            ],
            [
                ['db', 'domainName', 'moduleName'],
                'match',
                'pattern' => '/^\w+$/',
                'message' => 'Only word characters are allowed.',
            ],
            [
                [
                    'ns',
                    'recordBaseClass',
                    'queryBaseClass',
                    'entityBaseClass',
                    'repositoryBaseClass',
                ],
                'match',
                'pattern' => '/^[\w\\\\]+$/',
                'message' => 'Only word characters and backslashes are allowed.',
            ],
            [
                ['tableName'],
                'match',
                'pattern' => '/^([\w ]+\.)?([\w\* ]+)$/',
                'message' => 'Only word characters, and optionally spaces, an asterisk and/or a dot are allowed.',
            ],
            [['db'], 'validateDb'],
            [['ns'], 'validateNamespace'],
            [['tableName'], 'validateTableName'],
            [['domainName', 'moduleName'], 'validateDomainOrModuleName', 'skipOnEmpty' => false],
            [['recordBaseClass'], 'validateClass', 'params' => ['extends' => ActiveRecord::class]],
            [
                ['queryBaseClass'],
                'validateClass',
                'params' => ['extends' => ActiveQuery::class],
            ],
            [
                ['entityBaseClass'],
                'validateClass',
                'params' => ['extends' => Entity::class],
            ],
            [
                ['repositoryBaseClass'],
                'validateClass',
                'params' => ['extends' => EntitiesRepository::class],
            ],
            [
                ['generateRelations'],
                'in',
                'range' => [self::RELATIONS_NONE, self::RELATIONS_ALL, self::RELATIONS_ALL_INVERSE],
            ],
            [
                [
                    'generateLabelsFromComments',
                    'useTablePrefix',
                    'useSchemaName',
                    'generateQuery',
                    'generateRelationsFromCurrentSchema',
                ],
                'boolean',
            ],
            [['enableI18N'], 'boolean'],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array {
        return array_merge(parent::attributeLabels(), [
            'domainName' => 'Domain Name',
            'moduleName' => 'Module Name',
            'domainPath' => 'Domain Path',
            'queryBaseClass' => 'Query Base Class',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints(): array {
        return array_merge(parent::hints(), [
            'domainName' => 'This is the name of the domain to be generated. The name should not contain
                the namespace part as it is specified in "Namespace".',
            'moduleName' => 'This is the name of the application module to will contain generated domain.',
            'ns' => 'This is the namespace of the Domain class to be generated, e.g., <code>Module\Domain\Model\TableName</code>',
            'domainPath' => 'Path to wriatable directory where should be stored generated files, e.g., <code>@app/domain</code>',
            'recordBaseClass' => 'This is the base class of the new Record class. It should be a fully qualified namespaced class name.',
            'queryBaseClass' => 'This is the base class of the new Query class. It should be a fully qualified namespaced class name.',
            'entityBaseClass' => 'This is the base class of the new Entity class. It should be a fully qualified namespaced class name.',
            'repositoryBaseClass' => 'This is the base class of the new Repository class. It should be a fully qualified namespaced class name.',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes() {
        return [
            'template',
            'enableI18N',
            'messageCategory',
            'ns',
            'db',
            'domainPath',
            'recordBaseClass',
            'queryBaseClass',
            'entityBaseClass',
            'repositoryBaseClass',
            'generateRelations',
            'generateLabelsFromComments',
            'useTablePrefix',
        ];
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates(): array {
        return ['record.php', 'query.php', 'entity.php', 'repository.php'];
    }

    public function validateDomainOrModuleName(string $attribute): void {
        if ($this->isReservedKeyword($this->$attribute)) {
            $this->addError($attribute, '{attribute} cannot be a reserved PHP keyword.');
        }
        if ((empty($this->tableName) || substr_compare($this->tableName, '*', -1, 1)) && $this->$attribute == '') {
            $this->addError($attribute, '{attribute} cannot be blank if table name does not end with asterisk.');
        }
    }

    /**
     * @inheritdoc
     */
    public function generate(): array {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            $this->domainName = $this->generateDomainName($tableName);
            $this->createDomainFolder();

            // record:
            $recordClassName = $this->generateRecordClassName();
            $queryClassName = $this->generateQueryClassName();
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $recordClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];
            $files[] = new CodeFile(
                Yii::getAlias($this->domainPath) . '/' . $recordClassName . '.php',
                $this->render('record.php', $params)
            );

            // query:
            $params = [
                'className' => $queryClassName,
                'recordClassName' => $recordClassName,
            ];
            $files[] = new CodeFile(
                Yii::getAlias($this->domainPath) . '/' . $queryClassName . '.php',
                $this->render('query.php', $params)
            );

            // entity:
            $entityClassName = $this->generateEntityClassName();
            $params = [
                'className' => $entityClassName,
                'recordClassName' => $recordClassName,
                'tableSchema' => $tableSchema,
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];
            $files[] = new CodeFile(
                Yii::getAlias($this->domainPath) . '/' . $entityClassName . '.php',
                $this->render('entity.php', $params)
            );

            // repository:
            $repositoryClassName = $this->generateRepositoryClassName();
            $providerClassName = $this->generateProviderClassName();
            $params = [
                'className' => $repositoryClassName,
                'recordClassName' => $recordClassName,
                'queryClassName' => $queryClassName,
                'entityClassName' => $entityClassName,
                'providerClassName' => $providerClassName,
            ];
            $files[] = new CodeFile(
                Yii::getAlias($this->domainPath) . '/' . $repositoryClassName . '.php',
                $this->render('repository.php', $params)
            );
        }

        return $files;
    }

    /**
     * @inheritdoc
     */
    protected function getTableNames(): array {
        if ($this->tableNames !== null) {
            return $this->tableNames;
        }
        $db = $this->getDbConnection();
        if ($db === null) {
            return [];
        }
        $tableNames = [];
        if (strpos($this->tableName, '*') !== false) {
            if (($pos = strrpos($this->tableName, '.')) !== false) {
                $schema = substr($this->tableName, 0, $pos);
                $pattern = '/^' . str_replace('*', '\w+', substr($this->tableName, $pos + 1)) . '$/';
            } else {
                $schema = '';
                $pattern = '/^' . str_replace('*', '\w+', $this->tableName) . '$/';
            }

            foreach ($db->schema->getTableNames($schema) as $table) {
                if (preg_match($pattern, $table)) {
                    $tableNames[] = $schema === '' ? $table : ($schema . '.' . $table);
                }
            }
        } elseif (($table = $db->getTableSchema($this->tableName, true)) !== null) {
            $tableNames[] = $this->tableName;
            $this->classNames[$this->tableName] = $this->domainName;
        }

        return $this->tableNames = $tableNames;
    }

    protected function createDomainFolder(): void {
        $this->domainPath = $this->domainPath . '/' . $this->domainName;
        $path = Yii::getAlias($this->domainPath);
        mkdir($path);
        chmod($path, 0755);
    }

    protected function generateDomainName(string $tableName, bool $useSchemaName = null) {
        if (isset($this->classNames[$tableName])) {
            return $this->classNames[$tableName];
        }

        $schemaName = '';
        $fullTableName = $tableName;
        if (($pos = strrpos($tableName, '.')) !== false) {
            if (($useSchemaName === null && $this->useSchemaName) || $useSchemaName) {
                $schemaName = substr($tableName, 0, $pos) . '_';
            }
            $tableName = substr($tableName, $pos + 1);
        }

        $db = $this->getDbConnection();
        $patterns = [];
        $patterns[] = "/^{$db->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$db->tablePrefix}$/";
        if (strpos($this->tableName, '*') !== false) {
            $pattern = $this->tableName;
            if (($pos = strrpos($pattern, '.')) !== false) {
                $pattern = substr($pattern, $pos + 1);
            }
            $patterns[] = '/^' . str_replace('*', '(\w+)', $pattern) . '$/';
        }
        $className = $tableName;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $tableName, $matches)) {
                $className = $matches[1];
                break;
            }
        }

        $name = Inflector::singularize(Inflector::id2camel($schemaName . $className, '_'));

        return $this->classNames[$fullTableName] = $name;
    }

    protected function generateRecordClassName(): string {
        return $this->domainName . 'Record';
    }

    protected function generateQueryClassName(): string {
        return $this->domainName . 'Query';
    }

    protected function generateEntityClassName(): string {
        return $this->domainName . 'Entity';
    }

    protected function generateRepositoryClassName(): string {
        return $this->domainName . 'Repository';
    }

    protected function generateProviderClassName(): string {
        return $this->domainName . 'Provider';
    }

    /**
     * @inheritdoc
     */
    public function generateLabels(TableSchema $table): array {
        $labels = [];
        foreach ($table->columns as $column) {
            if ($this->generateLabelsFromComments && !empty($column->comment)) {
                $labels[$column->name] = $column->comment;
            } elseif (!strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $columnName = $column->name;
                if (strpos($columnName, 'ref_') === 0) {
                    $columnName = str_replace('ref_', '', $columnName);
                }
                $label = Inflector::camel2words($columnName);
                if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3) . ' ID';
                }
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }

    /**
     * @inheritdoc
     */
    protected function generateRelations(): array {
        if ($this->generateRelations === self::RELATIONS_NONE) {
            return [];
        }

        $db = $this->getDbConnection();
        $relations = [];
        foreach ($this->getSchemaNames() as $schemaName) {
            foreach ($db->getSchema()->getTableSchemas($schemaName) as $table) {
                $className = $this->generateClassName($table->fullName);
                foreach ($table->foreignKeys as $refs) {
                    $refTable = $refs[0];
                    $refTableSchema = $db->getTableSchema($refTable);
                    if ($refTableSchema === null) {
                        // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                        continue;
                    }
                    unset($refs[0]);
                    $fks = array_keys($refs);
                    $refClassName = $this->generateClassName($refTable);

                    // Add relation for this table
                    $link = $this->generateRelationLink(array_flip($refs));
                    $relationName = $this->generateRelationName($relations, $table, $fks[0], false);
                    $relationAlias = lcfirst($relationName);
                    $relations[$table->fullName][$relationName] = [
                        "return \$this->hasOne($refClassName::class, $link)->alias('$relationAlias');",
                        $refClassName,
                        false,
                    ];

                    // Add relation for the referenced table
                    $hasMany = $this->isHasManyRelation($table, $fks);
                    $link = $this->generateRelationLink($refs);
                    $relationName = $this->generateRelationName($relations, $refTableSchema, $className, $hasMany);
                    $relationAlias = lcfirst($relationName);
                    $relations[$refTableSchema->fullName][$relationName] = [
                        "return \$this->" . ($hasMany ? 'hasMany' : 'hasOne') . "($className::class, $link)->alias('$relationAlias');",
                        $className,
                        $hasMany,
                    ];
                }

                if (($junctionFks = $this->checkJunctionTable($table)) === false) {
                    continue;
                }

                $relations = $this->generateManyManyRelations($table, $junctionFks, $relations);
            }
        }

        if ($this->generateRelations === self::RELATIONS_ALL_INVERSE) {
            return $this->addInverseRelations($relations);
        }

        return $relations;
    }

    /**
     * @inheritdoc
     */
    private function generateManyManyRelations(TableSchema $table, array $fks, array $relations): array {
        $db = $this->getDbConnection();

        foreach ($fks as $pair) {
            list($firstKey, $secondKey) = $pair;
            $table0 = $firstKey[0];
            $table1 = $secondKey[0];
            unset($firstKey[0], $secondKey[0]);
            $className0 = $this->generateClassName($table0);
            $className1 = $this->generateClassName($table1);
            $table0Schema = $db->getTableSchema($table0);
            $table1Schema = $db->getTableSchema($table1);

            $link = $this->generateRelationLink(array_flip($secondKey));
            $viaLink = $this->generateRelationLink($firstKey);
            $relationName = $this->generateRelationName($relations, $table0Schema, key($secondKey), true);
            $relations[$table0Schema->fullName][$relationName] = [
                "return \$this->hasMany($className1::className(), $link)
                ->viaTable('" . $this->generateTableName($table->name) . "', $viaLink);",
                $className1,
                true,
            ];

            $link = $this->generateRelationLink(array_flip($firstKey));
            $viaLink = $this->generateRelationLink($secondKey);
            $relationName = $this->generateRelationName($relations, $table1Schema, key($firstKey), true);
            $relationAlias = lcfirst($relationName);
            $relations[$table1Schema->fullName][$relationName] = [
                "return \$this->hasMany($className0::class, $link)
                ->viaTable('" . $this->generateTableName($table->name) . "', $viaLink)->alias($relationAlias);",
                $className0,
                true,
            ];
        }

        return $relations;
    }

    /**
     * @inheritdoc
     */
    protected function generateRelationName(array $relations, TableSchema $table, string $key, bool $multiple): string {
        if (!empty($key) && substr_compare($key, 'id', -2, 2, true) === 0 && strcasecmp($key, 'id')) {
            $key = rtrim(substr($key, 0, -2), '_');
        }
        if (strpos($key, 'ref_') === 0) {
            $key = str_replace('ref_', '', $key);
            $key = Inflector::singularize($key);
        }
        if ($multiple) {
            $key = Inflector::pluralize($key);
        }
        $name = $rawName = Inflector::id2camel($key, '_');
        $i = 0;
        while (isset($table->columns[lcfirst($name)])) {
            $name = $rawName . ($i++);
        }
        while (isset($relations[$table->fullName][$name])) {
            $name = $rawName . ($i++);
        }

        return $name;
    }

    /**
     * @inheritdoc
     */
    public function generateString(string $string = '', array $placeholders = []): string {
        $string = addslashes($string);
        if ($this->enableI18N) {
            // If there are placeholders, use them
            if (!empty($placeholders)) {
                $ph = ', ' . VarDumper::export($placeholders);
            } else {
                $ph = '';
            }
            $str = "\$app->translate('" . $this->messageCategory . "', '" . $string . "'" . $ph . ")";
        } else {
            // No I18N, replace placeholders by real words, if any
            if (!empty($placeholders)) {
                $phKeys = array_map(function ($word) {
                    return '{' . $word . '}';
                }, array_keys($placeholders));
                $phValues = array_values($placeholders);
                $str = "'" . str_replace($phKeys, $phValues, $string) . "'";
            } else {
                // No placeholders, just the given string
                $str = "'" . $string . "'";
            }
        }

        return $str;
    }
}