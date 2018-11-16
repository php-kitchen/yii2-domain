<?php

namespace PHPKitchen\Domain\Generator\Domain\UI;

use PHPKitchen\Domain\Generator\Domain\ModelGenerator;

/* @var $this \yii\web\View */
/* @var $form \yii\widgets\ActiveForm */
/* @var $generator ModelGenerator */

$asset = DomainAsset::register($this);

echo $form->field($generator, 'tableName')->textInput(['table_prefix' => $generator->getTablePrefix()]);
echo $form->field($generator, 'domainName');
echo $form->field($generator, 'moduleName');
echo $form->field($generator, 'ns');
echo $form->field($generator, 'domainPath');
echo $form->field($generator, 'recordBaseClass');
echo $form->field($generator, 'queryBaseClass');
echo $form->field($generator, 'entityBaseClass');
echo $form->field($generator, 'repositoryBaseClass');
echo $form->field($generator, 'db');
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'generateRelations')->dropDownList([
    ModelGenerator::RELATIONS_NONE => 'No relations',
    ModelGenerator::RELATIONS_ALL => 'All relations',
    ModelGenerator::RELATIONS_ALL_INVERSE => 'All relations with inverse',
]);
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
echo $form->field($generator, 'useSchemaName')->checkbox();
