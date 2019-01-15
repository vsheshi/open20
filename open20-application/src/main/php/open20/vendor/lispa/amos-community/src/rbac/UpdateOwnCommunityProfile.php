<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */
namespace lispa\amos\community\rbac;

use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use yii\rbac\Item;
use yii\rbac\Rule;

/**
 * Class UpdateOwnUserProfile
 * @package lispa\amos\community\rbac
 */
class UpdateOwnCommunityProfile extends Rule
{
    public $name = 'isYourCommunityProfile';
    public $description = '';

    public function execute($user, $item, $params)
    {
        $post = \Yii::$app->getRequest()->post();
        $get = \Yii::$app->getRequest()->get();

        if (isset($get['id']) && $user == $get['id']) {
            return true;
        } elseif (isset($post['id']) && $user == $get['id']) {
            return true;
        }

        return false;
    }
}
