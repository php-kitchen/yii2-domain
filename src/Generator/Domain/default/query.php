<?php
/**
 * This is the template for generating the query class.
 */

/* @var $this yii\web\View */
/* @var $generator PHPKitchen\Domain\Generator\Domain\ModelGenerator */
/* @var $className string class name */
/* @var $entityClassName string related entity class name */

$baseClassName = substr($generator->queryBaseClass, strrpos($generator->queryBaseClass, '\\') + 1);
echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use <?= ltrim($generator->queryBaseClass, '\\') ?>;
use yii\db\BatchQueryResult;

/**
* This is the query class for [[<?= $entityClassName ?>]].
*
* @method <?= $entityClassName ?>|array|null one($db = null)
* @method <?= $entityClassName ?>[]|array all($db = null)
* @method <?= $entityClassName ?>[]|BatchQueryResult batch($batchSize = 100, $db = null)
* @method <?= $entityClassName ?>|BatchQueryResult each($batchSize = 100, $db = null)
* @method <?= $entityClassName ?>|null oneWithPk($pk)
*
* @see <?= $entityClassName . "\n" ?>
 */
class <?= $className ?> extends <?= $baseClassName ?> {
}