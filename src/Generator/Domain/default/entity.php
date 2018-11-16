<?php
/**
 * This is the template for generating the entity class.
 */

/* @var $this yii\web\View */
/* @var $generator PHPKitchen\Domain\Generator\Domain\ModelGenerator */
/* @var $className string class name */
/* @var $recordClassName string related record class name */
/* @var $tableSchema yii\db\TableSchema */

$recordFullClassName = $recordClassName;
if ($generator->ns !== $generator->queryNs) {
    $recordFullClassName = $generator->ns . '\\' . $recordFullClassName;
}
$baseClassName = substr($generator->entityBaseClass, strrpos($generator->entityBaseClass, '\\') + 1);
echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use <?= $recordFullClassName ?>;
use <?= ltrim($generator->entityBaseClass, '\\') ?>;

/**
 * Represents application <?= $recordClassName ?>.
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 *
 * @see <?= $recordClassName . "\n" ?>
*/
class <?= $className ?> extends <?= $baseClassName ?> {
}
