<?php
namespace PHPKitchen\Domain\Dev\View\Assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class SiteAsset extends AssetBundle {
    public function init() {
        $this->basePath = '@webroot';
        $this->baseUrl = '@web';
        $this->css = [
            'css/site.css',
        ];
        $this->js = [
        ];
        $this->depends = [
            YiiAsset::class,
            BootstrapAsset::class,
        ];
        parent::init();
    }
}
