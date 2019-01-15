<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\rbac
 * @category   CategoryName
 */

namespace lispa\amos\core\rbac;


use yii\helpers\ArrayHelper;
use Yii;
use yii\log\Logger;
use yii\db\Query;
use yii\rbac\Item;

class AuthManager extends \yii\rbac\DbManager {
    
    
    /**
     * 
     * @param return array
     */
    public function getUserIdsByRole($roleName) {
        $ids = parent::getUserIdsByRole($roleName);        
        foreach($this->getParents($roleName) as $parent) {
            if ($parent['type'] == Item::TYPE_ROLE) {
                $ids = ArrayHelper::merge($ids, $this->getUserIdsByRole($parent['name']));
            }
        }
        return $ids;
    }
    
    /**
     * 
     * @param string $roleName
     * @return array
     */
    public function getParents($roleName){
        $parents = [];
        
        try{
            $query = new Query;
            $parents = $query->select('b.*')
                ->from(['a' => $this->itemChildTable, 'b' => $this->itemTable])
                ->where('{{a}}.[[parent]]={{b}}.[[name]]')
                ->andwhere(['child' => $roleName])
                ->all($this->db);
        }catch(yii\base\Exception $ex){
           Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $parents;
    }
}
