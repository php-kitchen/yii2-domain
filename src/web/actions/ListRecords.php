<?php

namespace dekey\domain\web\actions;

use dekey\domain\web\base\Action;

/**
 * Represents
 *
 * @package dekey\domain\web
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class ListRecords extends Action {
    /**
     * @var callable a PHP callable that will be called to prepare a data provider that
     * should return a collection of the models. If not set, [[prepareDataProvider()]] will be used instead.
     * The signature of the callable should be:
     *
     * ```php
     * function ($dataProvider, $action) {
     *     // $dataProvider the data provider instance
     *     // $action is the action object currently running
     * }
     * ```
     *
     * The callable should return an instance of [[\yii\data\DataProviderInterface]].
     */
    public $prepareDataProvider;

    public function init() {
        $this->setViewFileIfNotSetTo('list');
    }

    public function run() {
        $controller = $this->controller;
        $dataProvider = $controller->createListingRecordsProvider();
        if (is_callable($this->prepareDataProvider)) {
            call_user_function_array($this->prepareDataProvider, [$dataProvider, $this]);
        }
        return $this->renderViewFile(compact('dataProvider'));
    }
}