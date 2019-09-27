# [Release 0.0.31](https://github.com/php-kitchen/yii2-domain/releases/tag/v0.0.31)

This release concentrated on a refactoring of  `PHPKitchen\Domain\Web\Base` namespace.

## BC BREAKS

Reorganized `PHPKitchen\Domain\Web\Base` namespace.
- `Action` moved to `PHPKitchen\Domain\Web\Base\Actions` 
- `EntityModificationAction` moved to `PHPKitchen\Domain\Web\Base\Actions` 
- `ListingModel` moved to `PHPKitchen\Domain\Web\Base\Models` 
- `RecoverableEntitiesListModel` moved to `PHPKitchen\Domain\Web\Base\Models` 
- `ViewModel` moved to `PHPKitchen\Domain\Web\Base\Models` 

## DEPRECATIONS

To prevend immediate failure of existing applications, following classes are kept for temporarly BC compatibiity and maked as deprecated at  `PHPKitchen\Domain\Web\Base` namespace:
- `Action`
- `EntityModificationAction` 
- `ListingModel`
- `RecoverableEntitiesListModel`
- `ViewModel`

## NEW FEATURES

### Updated actions hierarchy to become more flexible

Split response, repository and session related actions to mixins:
- `PHPKitchen\Domain\Web\Base\Mixins\RepositoryAccess`: provides generic repository management methods
- `PHPKitchen\Domain\Web\Base\Mixins\ResponseManagement`: provides generic response management methods
- `PHPKitchen\Domain\Web\Base\Mixins\SessionMessagesManagement`:provides generic session and flashes management methods

Extracted action hooks for successful and failed processing to mixin `PHPKitchen\Domain\Web\Base\Mixins\EntityActionHooks`

### Added new base actions
- `CallableAction`: for running strategies and callbacks
- `ServiceAction`: for running services

###  `Web\Base\Actions\Action` improvements

Added new rendering methods to utilize a controller's rendering functionality:
- `renderViewFileForAjax`
- `renderFile`
- `renderPartial`
- `renderAjax`

Added `printable`  to enable/disable rendering in action through `printView` method.
