<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

namespace lispa\amos\cwh\utility;

use lispa\amos\core\record\Record;
use lispa\amos\cwh\AmosCwh;
use lispa\amos\cwh\models\base\CwhNodiView;
use lispa\amos\cwh\models\CwhConfig;
use lispa\amos\cwh\models\CwhConfigContents;
use lispa\amos\cwh\models\CwhNodi;
use lispa\amos\cwh\models\CwhPubblicazioni;
use lispa\amos\cwh\models\CwhPubblicazioniCwhNodiEditoriMm;
use lispa\amos\cwh\models\CwhPubblicazioniCwhNodiValidatoriMm;
use lispa\amos\cwh\models\CwhRegolePubblicazione;
use lispa\amos\cwh\query\CwhActiveQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class CwhUtil
 * @package lispa\amos\cwh\utility
 */
class CwhUtil
{

    /**
     * Given the list of CwhNodes id (as array o string with comma separator), get the list as domains name separeted by comma
     *
     * @param mixed $cwhNodeIds
     * @return string
     */
    public static function getDomainNames($cwhNodeIds)
    {
        $domainNames = '';
        if (!empty($cwhNodeIds)) {
            if (is_array($cwhNodeIds)) {
                $domains = ArrayHelper::map(CwhNodi::find()->andWhere(['in', 'id', $cwhNodeIds])->all(),
                    'id', 'text');
            } else {
                $domains = ArrayHelper::map(CwhNodi::find()->andWhere("id in ('" . $cwhNodeIds . "')")->all(),
                    'id', 'text');
            }
            $domainNames = implode(', ', $domains);
        }
        return $domainNames;
    }

    /**
     * @param array|string $tagIds - array of tag ids or string containing tag ids separated by ','
     * @return string - list of tag names separated by ','
     */
    public static function getTagNames($tagIds)
    {

        $tagNames = '';
        $moduleTag = \Yii::$app->getModule('tag');
        if (isset($moduleTag) && !empty($tagIds)) {
            if (is_array($tagIds)) {
                $tags = ArrayHelper::map(\lispa\amos\tag\models\Tag::find()->andWhere([
                    'in',
                    'id',
                    $tagIds
                ])->all(),
                    'id', 'nome');
            } else {
                $tags = ArrayHelper::map(\lispa\amos\tag\models\Tag::find()->andWhere('id in (' . $tagIds . ')')->all(),
                    'id', 'nome');
            }
            $tagNames = implode(', ', $tags);
        }
        return $tagNames;
    }

    /**
     * @param int $publicationRuleId
     * @return string - publication rule label in translation
     */
    public static function getPublicationRuleLabel($publicationRuleId)
    {

        $name = '';
        $publicationRule = CwhRegolePubblicazione::findOne($publicationRuleId);
        if (!empty($publicationRule)) {
            $name = $publicationRule->nome;
        }
        return AmosCwh::t('amoscwh', $name);
    }

    /**
     * Query for network models in user network, given the network type configuration.
     *
     * @param int $cwhConfigId - id of network configuration (table cwh_config)
     * @param int|null $userId - if null logged userId is considered
     * @param bool|true $checkActive - if true check for only ACTIVE status in network-user relation table
     * @return ActiveQuery|null $networksQuery
     */
    public static function getUserNetworkQuery($cwhConfigId, $userId = null, $checkActive = true)
    {
        $cwhConfig = CwhConfig::findOne($cwhConfigId);
        if (!is_null($cwhConfig)) {
            if (is_null($userId)) {
                $userId = Yii::$app->user->id;
            }
            $network = Yii::createObject($cwhConfig->classname);
            $mmTable = $network->getMmTableName();
            if ($network->hasMethod('getUserNetworkQuery')) {
                $networksQuery = $network->getUserNetworkQuery($userId);
            } else {
                $networksQuery = $network->find()->innerJoin($mmTable,
                    $mmTable . '.' . $network->getMmUserIdFieldName() . '=' . $userId
                    . " AND " . $mmTable . '.' . $network->getMmNetworkIdFieldName() . '=' . $cwhConfig->tablename .'.id' )
                    ->andWhere($mmTable . '.deleted_at IS NULL')
                    ->andWhere($cwhConfig->tablename . '.deleted_at IS NULL');
            }
            if ($checkActive) {
                $mmTableSchema = Yii::$app->db->schema->getTableSchema($mmTable);
                if (isset($mmTableSchema->columns['status'])) {
                    $networksQuery->andWhere([$mmTable . '.status' => 'ACTIVE']);
                }
            }
            return $networksQuery;
        }
        return null;
    }


    /**
     * Query for publication rules enabled for logged user
     * @return \lispa\amos\cwh\models\query\CwhRegolePubblicazioneQuery|mixed $publicationRulesQuery
     */
    public static function getPublicationRulesQuery()
    {
        /** @var AmosCwh $cwhModule */
        $cwhModule = AmosCwh::getInstance();
        $scope = $cwhModule->getCwhScope();

        //if we are working under a specific network scope (eg. community dashboard)
        $scopeFilter = (empty($scope) ? false : true);

        $publicationRulesQuery = CwhRegolePubblicazione::find();
        //If module tag is not active, exclude publication rule based on tag
        $moduleTag = \Yii::$app->getModule('tag');
        if(!isset($moduleTag)){
            $publicationRulesQuery->excludeTag();
        }
        //if filter on publication rules by role is active
        // (rule public - to all users - is visible only to users having a specific role)
        if($cwhModule->regolaPubblicazioneFilter){
            $publicationRulesQuery->filterByRole();
        }
        //if working in a network scope only rules based on the network membership are available
        if ($scopeFilter) {
            $publicationRulesQuery->onlyNetwork();
        }
        return $publicationRulesQuery;
    }

    /**
     * Get the contents published for the specified network as an array
     * structure array[configContentInfo] => ContentModel[] - config content info to use as array key is configurable with parameter $arrayKeyFromConfigContentsField
     * array key not present if no content of that content type has been published for the network
     *
     * @param int - $configId - Id of Network CwhConfig
     * @param int $networkId - record if of the network in its own table (eg. community->id)
     * @param string $arrayKeyFromConfigContentsField - field of CwhConfigContents to use as array key. Default is tablename
     * @return array
     * eg.or returned array
     * [
     *    ['news'] => News[],
     *    ['documenti'] => Documenti[]
     * ]
     */
    public static function getNetworkContents($configId, $networkId, $arrayKeyFromConfigContentsField = 'tablename'){

        $cwhConfigContents = CwhConfigContents::find()->all();
        $contentArray = [];
        /** @var CwhConfigContents $cwhConfigContent */
        foreach ($cwhConfigContents as $cwhConfigContent){
            $query = new CwhActiveQuery($cwhConfigContent->classname);
            $query->filterByPublicationNetwork($configId, $networkId);
            if($query->count()) {
                $contentArray[$cwhConfigContent->$arrayKeyFromConfigContentsField] = $query->all();
            }
        }
        return $contentArray;
    }

    /**
     * Delete the contents published in the specified network scope.
     * In case more more scopes are set, only the specified network publication scope is deleted, not the content itself.
     *
     * @param int - $configId - Id of Network CwhConfig
     * @param int $networkId - record if of the network in its own table (eg. community->id)
     */
    public static function deleteNetworkContents($configId, $networkId){

        $contentArray = self::getNetworkContents($configId, $networkId, 'id');

        foreach ($contentArray as $configContentId => $contents) {
            /** @var Record $content */
            foreach ($contents as $content) {
                //the content has more than 1 publication scope
                // Don't delete le content itself, but only the publication scope of selected network
                if(count($content->destinatari) > 1 ){
                    $publication = CwhPubblicazioni::findOne(['cwh_config_contents_id' => $configContentId, 'content_id' => $content->id]);
                    if(!is_null($publication)){
                        $editorNode = CwhPubblicazioniCwhNodiEditoriMm::findOne(['cwh_pubblicazioni_id' => $publication->id, 'cwh_config_id' => $configId, 'cwh_network_id' => $networkId]);
                        if(!is_null($editorNode)){
                           $editorNode->delete();
                        }
                    }
                }else{
                    //published only in the network scope. Delete le content itself
                    $content->delete();
                }
            }
        }
        //there may be contents published with network validation scope but not publication scope.
        //eg. news published by a community but to all users (no specific publication scope)
        //we don't know if the news must be deleted, we just change to user validation scope. If to be deleted, will be user choose.
        $validatorNodes = CwhPubblicazioniCwhNodiValidatoriMm::find()->andWhere(['cwh_config_id' => $configId, 'cwh_network_id' => $networkId])->all();
        if(count($validatorNodes)){
            //get user scope configurations
            $cwhConfigUser = CwhConfig::findOne(['tablename' => 'user']);
            $cwhNodiIdPrefix = $cwhConfigUser->tablename.'-';
            $cwhConfigIdUser = $cwhConfigUser->id;
            /** @var CwhPubblicazioniCwhNodiValidatoriMm $validatorNode */
            foreach ($validatorNodes as $validatorNode){
                $userId = $validatorNode->cwhPubblicazioni->created_by;
                if(!is_null($userId)){
                    $validatorNode->cwh_config_id = $cwhConfigIdUser;
                    $validatorNode->cwh_network_id = $userId;
                    $validatorNode->cwh_nodi_id = $cwhNodiIdPrefix.$userId;
                    $validatorNode->save();
                }
            }
        }
    }

    /**
     * create cwh nodi view if not exists or regenerate it
     * regenerate the table cwh nodi (materialization of the view
     * Cwh nodi view contains all network records based on the queries of cwh config table.
     */
    public static function createCwhView()
    {
        $listaConf = CwhConfig::find()->asArray()->all();

        $sqlSelect = '( ';
        $numeroConf = count($listaConf);
        $i = 1;
        foreach ($listaConf as $conf){
            $sqlSelect .= $conf['raw_sql'];
            if ($i < $numeroConf) {
                $sqlSelect .= ' ) UNION ( ';
            }
            $i++;
        }
        $sqlSelect .= ' );';

        $sql = 'CREATE OR REPLACE VIEW cwh_nodi_view AS ' . $sqlSelect;

        $db = Yii::$app->getDb();
       
        $db->createCommand($sql)->execute();
        $db->createCommand()->truncateTable(CwhNodi::tableName())->execute();
        $db->createCommand('INSERT ' . CwhNodi::tableName() . ' SELECT * FROM ' . CwhNodiView::tablename())->execute();
    }

}