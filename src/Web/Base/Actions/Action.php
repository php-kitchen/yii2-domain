<?php

namespace PHPKitchen\Domain\Web\Base\Actions;

use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;

use PHPKitchen\Domain\Contracts\ResponseHttpStatus;
use PHPKitchen\Domain\Web\Base\Mixins\ResponseManagement;
use PHPKitchen\Domain\Web\Base\Mixins\RepositoryAccess;
use PHPKitchen\Domain\Web\Base\Mixins\SessionMessagesManagement;
use PHPKitchen\Domain\Web\Contracts\RepositoryAware;

use yii\helpers\Inflector;

/**
 * Represents a base class for all controller actions that utilize Yii2Domain features.
 *
 * Own properties:
 * @property int $requestStatusCore
 *
 * Base properties:
 * @property \PHPKitchen\Domain\Contracts\EntityCrudController|\yii\web\Controller $controller
 * @property \yii\web\Request $request
 *
 * @package PHPKitchen\Domain\Web\Base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Action extends \yii\base\Action implements ServiceLocatorAware, ContainerAware, RepositoryAware {
    use ServiceLocatorAccess;
    use ContainerAccess;
    use RepositoryAccess;
    use SessionMessagesManagement;
    use ResponseManagement;
    /**
     * @var string name of the view, which should be rendered
     */
    public $viewFile;
    /**
     * @var callable callback that prepares params for a view. Use it to extend default view params list.
     */
    public $prepareViewParams;
    /**
     * @var bool defines whether an action can be rendered or it just process request without printing any HTML and should
     * redirect to a next page using {@link redirectUrl}.
     */
    public $printable = true;
    /**
     * @var string default action to redirect using {@link redirectToNextPage}
     */
    protected $defaultRedirectUrlAction = 'index';
    /**
     * Checks whether action with specified ID exists in owner controller.
     *
     * @param string $id action ID.
     *
     * @return boolean whether action exists or not.
     */
    protected function isActionExistsInController($id): bool {
        $inlineActionMethodName = 'action' . Inflector::camelize($id);
        if ($this->controller->hasMethod($inlineActionMethodName) || array_key_exists($id, $this->controller->actions())) {
            return true;
        }

        return false;
    }

    protected function setViewFileIfNotSetTo($file) {
        $this->viewFile = isset($this->viewFile) ? $this->viewFile : $file;
    }

    /**
     * Prints page view file defined in {@link viewFile}.
     * Params being passed to view from {@link getDefaultViewParams}
     *
     * @return string page content
     */
    protected function printView() {
        return $this->printable ? $this->renderViewFile([]) : $this->redirectToNextPage();
    }

    protected function renderViewFile($params = []) {
        $viewParams = array_merge($this->getRequiredViewParams(), $this->getDefaultViewParams());
        $viewParams = array_merge($viewParams, $params);
        if (is_callable($this->prepareViewParams)) {
            $params = call_user_func($this->prepareViewParams, $viewParams, $this);
        }

        return $this->controller->render($this->viewFile, $params);
    }

    /**
     * Override this method to set params that should be passed to a view file.
     *
     * @return array view params
     */
    protected function getDefaultViewParams(): array {
        return [];
    }

    /**
     * Override this method to set required static params that should be passed to a view file.
     *
     * @return array view params
     */
    protected function getRequiredViewParams(): array {
        return [];
    }

    /**
     * Defines default redirect URL.
     *
     * If you need to change redirect action, set {@link defaultRedirectUrlAction} at action init.
     *
     * Override this method if you need to define custom format of URL.
     *
     * @return array url definition;
     */
    protected function prepareDefaultRedirectUrl() {
        return [$this->defaultRedirectUrlAction];
    }

    /**
     * Prepares params for a redirect URL callback set to {@link redirectUrl}
     *
     * Override this method if you need to define custom params.
     *
     * @return array url definition;
     */
    protected function prepareRedirectUrlCallbackParams(): array {
        return  [$this];
    }

    /**
     * @return mixed|\yii\console\Request|\yii\web\Request
     */
    protected function getRequest() {
        return $this->serviceLocator->request;
    }

    protected function getRequestStatusCore(): int {
        if ($this->request->isAjax) {
            return ResponseHttpStatus::OK;
        }

        return ResponseHttpStatus::FOUND;
    }
}