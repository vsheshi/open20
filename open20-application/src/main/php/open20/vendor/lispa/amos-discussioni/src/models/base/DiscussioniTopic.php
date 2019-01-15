<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

namespace lispa\amos\discussioni\models\base;

use lispa\amos\discussioni\AmosDiscussioni;
use lispa\amos\notificationmanager\record\NotifyRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the base-model class for table "discussioni_topic".
 *
 * @property integer $id
 * @property string $slug
 * @property string $titolo
 * @property string $testo
 * @property integer $hints
 * @property string $lat
 * @property string $lng
 * @property integer $in_evidenza
 * @property string $status
 * @property integer $image_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property integer $version
 *
 * @property \lispa\amos\discussioni\models\DiscussioniRisposte[] $discussioniRisposte
 * @property \lispa\amos\comments\models\Comment[] $discussionComments
 */
class DiscussioniTopic extends NotifyRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'discussioni_topic';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['testo'], 'string'],
            [['titolo', 'status'], 'required'],
            [['in_evidenza', 'hints', 'created_by', 'updated_by', 'deleted_by', 'version', 'image_id'], 'integer'],
            [['slug', 'created_at', 'updated_at', 'deleted_at', 'status'], 'safe'],
            [['titolo'], 'string', 'max' => 255]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => AmosDiscussioni::t('amosdiscussioni', 'ID'),
            'titolo' => AmosDiscussioni::t('amosdiscussioni', 'Titolo'),
            'testo' => AmosDiscussioni::t('amosdiscussioni', 'Testo'),
            'hints' => AmosDiscussioni::t('amosdiscussioni', 'Visualizzazioni'),
            'lat' => AmosDiscussioni::t('amosdiscussioni', 'Latitudine'),
            'lng' => AmosDiscussioni::t('amosdiscussioni', 'Longitudine'),
            'in_evidenza' => AmosDiscussioni::t('amosdiscussioni', 'In evidenza'),
            'status' => AmosDiscussioni::t('amosdiscussioni', 'Stato'),
            'image_id' => AmosDiscussioni::t('amosdiscussioni', 'Immagine'),
            'created_at' => AmosDiscussioni::t('amosdiscussioni', 'Creato il'),
            'updated_at' => AmosDiscussioni::t('amosdiscussioni', 'Aggiornato il'),
            'deleted_at' => AmosDiscussioni::t('amosdiscussioni', 'Cancellato il'),
            'created_by' => AmosDiscussioni::t('amosdiscussioni', 'Creato da'),
            'updated_by' => AmosDiscussioni::t('amosdiscussioni', 'Aggiornato da'),
            'deleted_by' => AmosDiscussioni::t('amosdiscussioni', 'Cancellato da'),
            'version' => AmosDiscussioni::t('amosdiscussioni', 'Versione numero'),
        ]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     * @deprecated from version 1.5. Use [[DiscussioniTopic::getDiscussionComments()]] instead of this.
     */
    public function getDiscussioniRisposte()
    {
        return $this->hasMany(\lispa\amos\discussioni\models\DiscussioniRisposte::className(), ['discussioni_topic_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscussionComments()
    {
        return $this->hasMany(\lispa\amos\comments\models\Comment::className(), ['context_id' => 'id'])
            ->andWhere(['context' => \lispa\amos\discussioni\models\DiscussioniTopic::className()]);
    }
}
