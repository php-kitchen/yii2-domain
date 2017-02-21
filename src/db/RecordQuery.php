<?php

namespace dekey\domain\db;

use dekey\di\mixins\ContainerAccess;
use dekey\di\mixins\ServiceLocatorAccess;
use dekey\domain\db\mixins\QueryConditionBuilderAccess;
use dekey\domain\db\mixins\RecordQueryFunctions;
use yii\db\ActiveQuery;
use dekey\domain\contracts;

/**
 * Represents
 *
 * @package dekey\domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class RecordQuery extends ActiveQuery implements contracts\Specification, contracts\RecordQuery {
    use QueryConditionBuilderAccess;
    use RecordQueryFunctions;
    use ContainerAccess;
    use ServiceLocatorAccess;
}