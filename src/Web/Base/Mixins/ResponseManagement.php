<?php

namespace PHPKitchen\Domain\Web\Base\Mixins;

/**
 * Represent mixin that adds support for redirecting response to an another page.
 *
 * Parent properties:
 * @property \PHPKitchen\Domain\Contracts\EntityCrudController|\yii\web\Controller $controller
 *
 * @package PHPKitchen\Domain\Web\Base\Mixins
 */
trait ResponseManagement {
    public $redirectUrl;

    /**
     * Implement this method to define default URL to redirect using {@link redirectToNextPage}.
     *
     * @return mixed URL to redirect
     */
    abstract protected function prepareDefaultRedirectUrl();
    /**
     * Implement this method to define params being passed to callback set into {@link redirectUrl}.
     *
     * @return mixed URL to redirect
     */
    abstract protected function prepareRedirectUrlCallbackParams(): array;

    /**
     * Redirects to a next page based on URL defined at {@link redirectUrl} or defined by {@link redirectToNextPage}.
     *
     * @return \yii\web\Response
     */
    protected function redirectToNextPage() {
        if (null === $this->redirectUrl) {
            $redirectUrl = $this->prepareDefaultRedirectUrl();
        } elseif (is_callable($this->redirectUrl)) {
            $callbackParams = $this->prepareRedirectUrlCallbackParams();
            $redirectUrl = call_user_func($this->redirectUrl, ...$callbackParams);
        } else {
            $redirectUrl = $this->redirectUrl;
        }

        return $this->controller->redirect($redirectUrl, $this->getRequestStatusCore());
    }

}