<?php

namespace PHPKitchen\Domain\Dev\App\Controllers;

use yii\web\Controller;
use yii\web\ErrorAction;

class SiteController extends Controller {
    public function init() {
        $this->layout = '@views/layouts/main';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        return $this->render('index');
    }
}
