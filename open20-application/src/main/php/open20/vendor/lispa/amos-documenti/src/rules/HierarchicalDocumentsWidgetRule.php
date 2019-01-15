<?php

/**
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\documenti
 * @category   CategoryName
 */

namespace lispa\amos\documenti\rules;

use yii\rbac\Rule;

class HierarchicalDocumentsWidgetRule extends Rule
{
    public $name = 'hierarchicalDocumentsWidget';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        return true;
    }
}
