<?php

namespace PHPKitchen\Domain\Generator\Domain;

/* @var $this \yii\web\View */
/* @var $form \yii\widgets\ActiveForm */
/* @var $generator Generator */

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
    Generator::RELATIONS_NONE => 'No relations',
    Generator::RELATIONS_ALL => 'All relations',
    Generator::RELATIONS_ALL_INVERSE => 'All relations with inverse',
]);
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
echo $form->field($generator, 'useSchemaName')->checkbox();
