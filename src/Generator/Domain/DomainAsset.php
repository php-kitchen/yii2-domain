<?php

namespace PHPKitchen\Domain\Generator\Domain;

use yii\gii\GiiAsset;
use yii\web\AssetBundle;

/**
 * This declares the asset files required by Domain generator.
 *
 * @package PHPKitchen\Domain\Generator\Domain
 * @author Dmitry Bukavin <djaconda@bitfocus.com>
 */
class DomainAsset extends AssetBundle {
    public $sourcePath = '@vendor/php-kitchen/yii2-domain/src/Generator/Domain/assets';
    public $js = [
        'gii.js',
    ];
    public $depends = [
        GiiAsset::class,
    ];
}