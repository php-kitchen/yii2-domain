<?php

namespace PHPKitchen\Domain\Generator\Domain\UI;

use yii\gii\GiiAsset;
use yii\web\AssetBundle;

/**
 * This declares the asset files required by Domain generator.
 *
 * @package PHPKitchen\Domain\Generator\Domain
 * @author Dmitry Bukavin <djaconda@bitfocus.com>
 */
class DomainAsset extends AssetBundle {
    public $sourcePath = __DIR__ . '/assets';
    public $js = [
        'gii.js',
    ];
    public $depends = [
        GiiAsset::class,
    ];
}