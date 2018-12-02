<?php
/**
 * This is the template for generating the entity class.
 */

/* @var $this yii\web\View */
/* @var $generator PHPKitchen\Domain\Generator\Domain\ModelGenerator */
/* @var $className string class name */
/* @var $recordClassName string related record class name */
/* @var $queryClassName string related entity class name */
/* @var $entityClassName string related entity class name */
/* @var $providerClassName string related entity class name */

$baseClassName = substr($generator->repositoryBaseClass, strrpos($generator->repositoryBaseClass, '\\') + 1);
echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use <?= ltrim($generator->repositoryBaseClass, '\\') ?>;
use PHPKitchen\Domain\DB\Finder;

/**
 * Represents {@link <?= $entityClassName ?>} repository.
 *
 * @method <?= $queryClassName ?>|Finder find()
 * @method <?= $entityClassName ?> findOneWithPk($pk)
 * @method <?= $entityClassName ?>[] findAll()
 * @method <?= $entityClassName ?>[] each($batchSize = 100)
* @method <?= $entityClassName ?> createNewEntity()
 * @method <?= $providerClassName ?> getEntitiesProvider()
*/
class <?= $className ?> extends <?= $baseClassName ?> {
}