<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\chat
 * @category   CategoryName
 */

namespace lispa\amos\chat\models\search;

use lispa\amos\chat\ModelDataProvider;
use lispa\amos\chat\models\User;
use yii\db\ActiveQuery;
use lispa\amos\admin\models\UserContact;

/**
 * Class UserContactsQuery
 * @package lispa\amos\chat\models\search
 */
class UserContactsQuery extends User
{
    /**
     * @param int $userId Logged user id.
     * @return ModelDataProvider
     */
    public static function getUserContacts($userId)
    {
        $query = static::findUserContacts($userId);

        return new ModelDataProvider([
            'query' => $query,
            'key' => 'id'
        ]);
    }

    /**
     * @param $userId
     * @return ActiveQuery
     */
    public static function findUserContacts($userId)
    {
        $query = null;
        $onlyNetworkUsers = \Yii::$app->getModule('chat')->onlyNetworkUsers;

        if($onlyNetworkUsers){
            $contactsInvited =
                User::find()->innerJoin('user_contact', 'user.id = user_contact.contact_id')
                    ->innerJoin('user_profile', 'user_profile.user_id = user.id')
                    ->andWhere('user_contact.deleted_at IS NULL AND user_profile.deleted_at IS NULL')
                    ->andWhere("user_contact.user_id = ".$userId)->andWhere([ 'user_contact.status' => UserContact::STATUS_ACCEPTED])
                    ->andWhere(['attivo' => 1]);

            $contactsInviting =
                User::find()->innerJoin('user_contact', 'user.id = user_contact.user_id')
                    ->innerJoin('user_profile', 'user_profile.user_id = user.id')
                    ->andWhere('user_contact.deleted_at IS NULL AND user_profile.deleted_at IS NULL')
                    ->andWhere("user_contact.contact_id = ".$userId)->andWhere(['user_contact.status' => UserContact::STATUS_ACCEPTED])
                    ->andWhere(['attivo' => 1]);

            $query = $contactsInvited->union($contactsInviting);
            $query->orderBy("cognome, nome");

        }else {
            $query =  User::find()
                ->joinWith("profile")
                ->andWhere("user_id != :userId", ['userId' => $userId])
                ->orderBy("cognome, nome");
        }
        return $query;
    }
}