<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\rules
 * @category   CategoryName
 */

namespace lispa\amos\admin\rules;

use yii\rbac\Rule;

/**
 * Class ShowCommunityManagerWidgetRule
 * @package lispa\amos\admin\rules
 */
class ShowCommunityManagerWidgetRule extends Rule
{
    public $name = 'showCommunityManagerWidget';
    
    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $communityModule = \Yii::$app->getModule('community');
        return (!is_null($communityModule));
    }
}
