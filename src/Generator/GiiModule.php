<?php

namespace PHPKitchen\Domain\Generator;

/**
 * Class GiiModule
 *
 * @package PHPKitchen\Domain\Generator
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class GiiModule extends \yii\gii\Module {
    public function init() {
        parent::init();

        $this->viewPath = '@app/../../vendor/yiisoft/yii2-gii/src/views';
    }

    public function bootstrap($app) {
        parent::bootstrap($app);

        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                [
                    'class' => 'yii\web\UrlRule',
                    'pattern' => $this->id,
                    'route' => $this->id . '/default/index',
                ],
                [
                    'class' => 'yii\web\UrlRule',
                    'pattern' => $this->id . '/<id:[\w\-]+>',
                    'route' => $this->id . '/default/view',
                ],
                [
                    'class' => 'yii\web\UrlRule',
                    'pattern' => $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>',
                    'route' => $this->id . '/<controller>/<action>',
                ],
            ], false);
        }
    }
}