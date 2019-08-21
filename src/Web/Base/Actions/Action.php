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
 *
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

        return $this->controller->hasMethod($inlineActionMethodName) || array_key_exists($id, $this->controller->actions());
    }

    protected function setViewFileIfNotSetTo($file) {
        $this->viewFile = isset($this->viewFile) ? $this->viewFile : $file;
    }

    /**
     * Prints page view file defined at {@link viewFile}.
     * Params being passed to view from {@link prepareViewContext} and {@link getDefaultViewParams}
     *
     * @return string the rendering result.
     */
    protected function printView() {
        return $this->printable ? $this->renderViewFile([]) : $this->redirectToNextPage();
    }

    /**
     * Renders a view defined at {@link viewFile} and applies layout if available.
     *
     *
     * @param array $params the parameters (name-value pairs) that should be made available in the view.
     * Params are extended by {@link prepareViewContext} and {@link getDefaultViewParams}
     *
     * @return string the rendering result.
     */
    protected function renderViewFile($params = []) {
        return $this->controller->render($this->viewFile, $this->prepareParamsForViewFile($params));
    }

    /**
     * Renders a view defined at {@link viewFile} without applying layout.
     * It will inject into the rendering result JS/CSS scripts and files which are registered with
     * the view.
     *
     * @param array $params the parameters (name-value pairs) that should be made available in the view.
     * Params are extended by {@link prepareViewContext} and {@link getDefaultViewParams}
     *
     * @return string the rendering result.
     */
    protected function renderViewFileForAjax($params = []) {
        return $this->controller->renderAjax($this->viewFile, $this->prepareParamsForViewFile($params));
    }

    /**
     * Prepares params for {@link viewFile} extending them wit the ones defined by  {@link prepareViewContext}
     * and {@link getDefaultViewParams}.
     *
     * @param $params name-value pairs
     *
     * @return array parameters (name-value pairs)
     */
    protected function prepareParamsForViewFile($params): array {
        $viewParams = array_merge($this->prepareViewContext(), $this->getDefaultViewParams());
        $viewParams = array_merge($viewParams, $params);
        if (is_callable($this->prepareViewParams)) {
            $params = call_user_func($this->prepareViewParams, $viewParams, $this);
        }

        return $params;
    }

    /**
     * Renders a view file.
     *
     * @param array $params the parameters (name-value pairs) that should be made available in the view.
     * Params are extended by {@link prepareViewContext}.
     *
     * @return string the rendering result.
     */
    protected function renderFile($params = []) {
        $params = array_merge($this->prepareViewContext(), $params);

        return $this->controller->renderFile($this->viewFile, $params);
    }

    /**
     * Renders a file without applying layout.
     *
     * @param array $params the parameters (name-value pairs) that should be made available in the view.
     * Params are extended by {@link prepareViewContext}.
     *
     * @return string the rendering result.
     */
    protected function renderPartial($params = []) {
        $params = array_merge($this->prepareViewContext(), $params);

        return $this->controller->renderPartial($this->viewFile, $params);
    }

    /**
     * Renders a view in response to an AJAX request.
     *
     * This method is similar to {@link renderPartial} except that it will inject into
     * the rendering result with JS/CSS scripts and files which are registered with the view.
     * For this reason, you should use this method instead of {@link renderPartial} to render
     * a view to respond to an AJAX request.
     *
     * @param array $params the parameters (name-value pairs) that should be made available in the view.
     * Params are extended by {@link prepareViewContext}.
     *
     * @return string the rendering result.
     */
    protected function renderAjax($params = []) {
        $params = array_merge($this->prepareViewContext(), $params);

        return $this->controller->renderAjax($this->viewFile, $params);
    }

    /**
     * Override this method to set params that should be passed to a view file defined at {@link viewFile}.
     *
     * @return array of view params (name-value pairs).
     */
    protected function getDefaultViewParams(): array {
        return [];
    }

    /**
     * Override this method to set variables that will be passed to any file rendered by
     * an action.
     *
     * @return array of view params (name-value pairs).
     */
    protected function prepareViewContext(): array {
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