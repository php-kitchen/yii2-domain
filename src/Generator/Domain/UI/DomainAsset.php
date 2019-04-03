<?php

namespace PHPKitchen\Domain\Generator\Domain\UI;

use yii\gii\GiiAsset;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * This declares the asset files required by Domain generator.
 *
 * @package PHPKitchen\Domain\Generator\Domain
 * @author Dmitry Bukavin <djaconda@bitfocus.com>
 */
class DomainAsset extends AssetBundle {
    public $sourcePath = __DIR__ . '/assets';
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
    public $js = [
        'gii.js',
    ];
    public $depends = [
        GiiAsset::class,
    ];
}