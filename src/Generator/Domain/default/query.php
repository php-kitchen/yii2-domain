<?php
/**
 * This is the template for generating the query class.
 */

/* @var $this yii\web\View */
/* @var $generator PHPKitchen\Domain\Generator\Domain\Generator */
/* @var $className string class name */
/* @var $recordClassName string related record class name */

$recordFullClassName = $recordClassName;
if ($generator->ns !== $generator->queryNs) {
    $recordFullClassName = $generator->ns . '\\' . $recordFullClassName;
}
$baseClassName = substr($generator->queryBaseClass, strrpos($generator->queryBaseClass, '\\') + 1);
echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use <?= $recordFullClassName ?>;
use <?= ltrim($generator->queryBaseClass, '\\') ?>;
use yii\db\BatchQueryResult;

/**
 * This is the query class for [[<?= $recordClassName ?>]].
 *
 * @method <?= $recordClassName ?>|array|null one($db = null)
 * @method <?= $recordClassName ?>[]|array all($db = null)
 * @method <?= $recordClassName ?>[]|BatchQueryResult batch($batchSize = 100, $db = null)
 * @method <?= $recordClassName ?>|BatchQueryResult each($batchSize = 100, $db = null)
 * @method <?= $recordClassName ?>|null oneWithPk($pk)
 *
 * @see <?= $recordClassName . "\n" ?>
 */
class <?= $className ?> extends <?= $baseClassName ?> {
}