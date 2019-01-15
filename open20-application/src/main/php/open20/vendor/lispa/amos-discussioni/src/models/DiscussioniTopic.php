<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni\models
 * @category   CategoryName
 */

namespace lispa\amos\discussioni\models;

use lispa\amos\admin\models\UserProfile;
use lispa\amos\attachments\behaviors\FileBehavior;
use lispa\amos\comments\models\Comment;
use lispa\amos\comments\models\CommentInterface;
use lispa\amos\core\interfaces\ContentModelInterface;
use lispa\amos\core\interfaces\ViewModelInterface;
use lispa\amos\core\user\User;
use lispa\amos\core\views\toolbars\StatsToolbarPanels;
use lispa\amos\discussioni\AmosDiscussioni;
use lispa\amos\discussioni\i18n\grammar\DiscussionTopicGrammar;
use lispa\amos\discussioni\models\base\DiscussioniTopic as DiscussioniTopicBase;
use lispa\amos\discussioni\widgets\icons\WidgetIconDiscussioniDashboard;
use lispa\amos\notificationmanager\behaviors\NotifyBehavior;
use lispa\amos\workflow\behaviors\WorkflowLogFunctionsBehavior;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\log\Logger;

/**
 * This is the model class for table "discussioni_topic".
 *
 * @method \cornernote\workflow\manager\components\WorkflowDbSource getWorkflowSource()
 * @method \yii\db\ActiveQuery hasOneFile($attribute = 'file', $sort = 'id')
 * @method \yii\db\ActiveQuery hasMultipleFiles($attribute = 'file', $sort = 'id')
 * @method string|null getRegolaPubblicazione()
 * @method array getTargets()
 *
 * @property \lispa\amos\admin\models\UserProfile $lastCommentUser
 * @property \lispa\amos\comments\models\Comment $lastComment
 * @property string $lastCommentDate
 * @property \lispa\amos\comments\models\Comment[] $lastComments
 */
class DiscussioniTopic extends DiscussioniTopicBase implements ContentModelInterface, CommentInterface, ViewModelInterface
{
    const DISCUSSIONI_WORKFLOW = 'DiscussioniTopicWorkflow';
    const DISCUSSIONI_WORKFLOW_STATUS_BOZZA = 'DiscussioniTopicWorkflow/BOZZA';
    const DISCUSSIONI_WORKFLOW_STATUS_DAVALIDARE = 'DiscussioniTopicWorkflow/DAVALIDARE';
    const DISCUSSIONI_WORKFLOW_STATUS_ATTIVA = 'DiscussioniTopicWorkflow/ATTIVA';
    const DISCUSSIONI_WORKFLOW_STATUS_DISATTIVA = 'DiscussioniTopicWorkflow/DISATTIVA';
    
    /**
     * All the scenarios listed below are for the wizard.
     */
    const SCENARIO_INTRODUCTION = 'scenario_introduction';
    const SCENARIO_DETAILS = 'scenario_details';
    const SCENARIO_PUBLICATION = 'scenario_publication';
    const SCENARIO_SUMMARY = 'scenario_summary';
    
    /**
     * @var $distance
     */
    public $distance;
    
    /**
     * @var string $regola_pubblicazione Regola di pubblicazione
     */
    public $regola_pubblicazione;
    
    /**
     * @var string $destinatari Destinatari
     */
    public $destinatari;
    
    /**
     * @var string $validatori Validatori
     */
    public $validatori;
    
    /**
     * @var string $destinatari_pubblicazione Destinatari pubblicazione
     */
    public $destinatari_pubblicazione;
    
    /**
     * @var string $destinatari_notifiche Destinatari notifiche
     */
    public $destinatari_notifiche;
    
    /**
     * @var $discussionsTopicImage
     */
    private $discussionsTopicImage;
    
    /**
     * @var $attachments
     */
    private $discussionsAttachments;
    
    /**
     * @var $attachmentsForItemView
     */
    private $discussionsAttachmentsForItemView;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            $this->status = $this->getWorkflowSource()->getWorkflow(self::DISCUSSIONI_WORKFLOW)->getInitialStatusId();
        }
    }
    
    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'titolo'
        ];
    }
    
    /**
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['discussionsTopicImage'], 'file', 'extensions' => 'jpeg, jpg, png, gif', 'maxFiles' => 1],
            [['discussionsAttachments'], 'file', 'maxFiles' => 0],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'regola_pubblicazione' => AmosDiscussioni::t('amosdiscussioni', 'Pubblicata per'),
            'destinatari' => AmosDiscussioni::t('amosdiscussioni', 'Per l\'ente'),
            'discussionsTopicImage' => AmosDiscussioni::t('amosdiscussioni', 'Discussion image'),
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DETAILS] = [
            'titolo',
            'testo',
            'discussionsTopicImage'
        ];
        $scenarios[self::SCENARIO_PUBLICATION] = [
            'destinatari_pubblicazione',
        ];
        $scenarios[self::SCENARIO_SUMMARY] = [
            'status'
        ];
        return $scenarios;
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'slug' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'titolo',
                'ensureUnique' => true
                // 'slugAttribute' => 'slug',
            ],
            'fileBehavior' => [
                'class' => FileBehavior::className()
            ],
            'workflow' => [
                'class' => SimpleWorkflowBehavior::className(),
                'defaultWorkflowId' => self::DISCUSSIONI_WORKFLOW,
                'propagateErrorsToModel' => true,
            ],
            'workflowLog' => [
                'class' => WorkflowLogFunctionsBehavior::className()
            ],
            'NotifyBehavior' => [
                'class' => NotifyBehavior::className(),
                'conditions' => [],
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

    }
    
    /**
     * Getter for $this->discussionsTopicImage;
     *
     */
    public function getDiscussionsTopicImage()
    {
        if(empty($this->discussionsTopicImage)){
            $this->discussionsTopicImage = $this->hasOneFile('discussionsTopicImage')->one();
        }
        return $this->discussionsTopicImage;
    }

    /**
     * @param $image
     */
    public function setDiscussionsTopicImage($image){
        $this->discussionsTopicImage = $image;
    }
    
    /**
     * Getter for $this->attachments;
     *
     */
    public function getDiscussionsAttachments()
    {
        if(empty($this->discussionsAttachments)){
            $this->discussionsAttachments = $this->hasMultipleFiles('discussionsAttachments')->one();
        }
        return $this->discussionsAttachments;
    }

    /**
     * @param $attachments
     */
    public function setDiscussionsAttachments($attachments){
        $this->discussionsAttachments = $attachments;
    }

    /**
     *
     */
    public function getDiscussionsAttachmentsForItemView(){
        if(empty($this->discussionsAttachmentsForItemView)){
            $this->discussionsAttachmentsForItemView = $this->hasMultipleFiles('discussionsAttachments')->all();
        }
        return $this->discussionsAttachmentsForItemView;
    }

    /**
     * @param $attachments
     */
    public function setDiscussionsAttachmentsForItemView($attachments){
        $this->discussionsAttachmentsForItemView = $attachments;
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!Yii::$app instanceof \yii\console\Application) {
                if (!isset($this->lat) && !isset($this->lng)) {
                    $this->setAttribute('lat', $this->getLatitude());
                    $this->setAttribute('lng', $this->getLongitude());
                }
            }
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @return mixed
     */
    protected function getLatitude()
    {
        /** @var User $UserIdentity */
        $UserIdentity = Yii::$app->getUser()->getIdentity();
        $UserProfile = $UserIdentity->getUserProfile()->one();
        return $UserProfile->domicilio_lat;
    }
    
    /**
     * @return mixed
     */
    protected function getLongitude()
    {
        /** @var User $UserIdentity */
        $UserIdentity = Yii::$app->getUser()->getIdentity();
        $UserProfile = $UserIdentity->getUserProfile()->one();
        return $UserProfile->domicilio_lon;
    }
    
    /**
     * @return null|ActiveQuery
     * @deprecated from version 1.5. Use [[Record::getCreatedUserProfile()]] instead of this.
     */
    public function getCreatoreDiscussione()
    {
        $modelClass = \lispa\amos\admin\AmosAdmin::instance()->createModel('UserProfile');
        return $this->hasOne($modelClass::className(), ['user_id' => 'created_by']);
    }
    
    /**
     * @return ActiveQuery
     * @deprecated from version 1.5. Use [[DiscussioniTopic::getLastCommentUser()]] instead of this.
     */
    public function getUtenteUltimaRisposta()
    {
        $modelClass = \lispa\amos\admin\AmosAdmin::instance()->createModel('UserProfile');
        return $this->hasOne($modelClass::className(), ['user_id' => 'created_by'])->viaTable($this->getUltimaRisposta(), ['discussioni_topic_id' => 'id']);
    }
    
    /**
     * @return ActiveQuery
     * @deprecated from version 1.5. Use [[DiscussioniTopic::getLastComment()]] instead of this.
     */
    public function getUltimaRisposta()
    {
        return $this->hasOne(DiscussioniRisposte::className(), ['discussioni_topic_id' => 'id'])->where(['created_at' => $this->getDataUltimaRisposta()]);
    }
    
    /**
     * @return mixed
     * @deprecated from version 1.5. Use [[DiscussioniTopic::getLastCommentDate()]] instead of this.
     */
    public function getDataUltimaRisposta()
    {
        return $this->hasOne(DiscussioniRisposte::className(), ['discussioni_topic_id' => 'id'])->max('created_at');
    }
    
    /**
     * @return ActiveQuery
     * @deprecated from version 1.5. Use [[DiscussioniTopic::getLastComments()]] instead of this.
     */
    public function getUltimeRisposte()
    {
        return $this->hasOne(DiscussioniRisposte::className(), ['discussioni_topic_id' => 'id'])->orderBy(['created_at' => SORT_DESC])->limit(3);
    }
    
    /**
     * @param \lispa\amos\discussioni\models\DiscussioniRisposte $risposta
     * @return ActiveQuery
     * @deprecated from version 1.5. Use [[DiscussioniTopic::getCommentCreatorUser()]] instead of this.
     */
    public function getUtenteRisposta(DiscussioniRisposte $risposta)
    {
        $modelClass = \lispa\amos\admin\AmosAdmin::instance()->createModel('UserProfile');
        return $modelClass::find()->where(['user_id' => $risposta->created_by]);
    }
    
    /**
     * @return array
     * @deprecated from version 1.5. Use [[DiscussioniTopic::commentsUsersAvatars()]] instead of this.
     */
    public function avatarsUtenti()
    {
        $avatars = [];
        /** @var \yii\db\ActiveQuery $q */
        $q = DiscussioniRisposte::find()->where(['discussioni_topic_id' => $this->id])->groupBy(['created_by'])->limit(4);
        $listRecord = $q->all();
        foreach ($listRecord as $record) {
            $usr = UserProfile::find()->where(['user_id' => $record->created_by])->one();
            if ($usr) {
                $avatars[] = $usr;
            }
        }
        return $avatars;
    }
    

    
    /**
     * @inheritdoc
     */
    public function getGridViewColumns()
    {
        return [
            'titolo' => [
                'attribute' => 'titolo',
                'headerOptions' => [
                    'id' => 'titolo'
                ],
                'contentOptions' => [
                    'headers' => 'titolo'
                ]
            ],
            'testo' => [
                'attribute' => 'testo',
                'format' => 'html',
                'headerOptions' => [
                    'id' => 'testo'
                ],
                'contentOptions' => [
                    'headers' => 'testo'
                ]
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function getViewUrl()
    {
        return "discussioni/discussioni-topic/partecipa";
    }
    
    /**
     * @inheritdoc
     */
    public function getToValidateStatus()
    {
        return self::DISCUSSIONI_WORKFLOW_STATUS_DAVALIDARE;
    }
    
    /**
     * @inheritdoc
     */
    public function getValidatedStatus()
    {
        return self::DISCUSSIONI_WORKFLOW_STATUS_ATTIVA;
    }
    
    /**
     * @inheritdoc
     */
    public function getDraftStatus()
    {
        return self::DISCUSSIONI_WORKFLOW_STATUS_BOZZA;
    }

    /**
     * @inheritdoc
     */
    public function getValidatorRole()
    {
        return 'VALIDATORE_DISCUSSIONI';
    }
    
    public function getPluginWidgetClassname()
    {
        return WidgetIconDiscussioniDashboard::className();
    }
    
    /**
     * @since 1.5 First time this was introduced.
     * @return bool
     */
    public function isCommentable()
    {
        return true;
    }
    
    /**
     * @since 1.5 First time this was introduced.
     * @return ActiveQuery
     */
    public function getLastCommentUser()
    {
        return (!is_null($this->lastComment) ? $this->lastComment->getCreatedUserProfile() : null);
    }
    
    /**
     * @since 1.5 First time this was introduced.
     * @return ActiveQuery
     */
    public function getLastComment()
    {
        return $this->hasOne(\lispa\amos\comments\models\Comment::className(), ['context_id' => 'id'])
            ->where(['created_at' => $this->getLastCommentDate()])
            ->andWhere(['context' => \lispa\amos\discussioni\models\DiscussioniTopic::className()]);
    }
    
    /**
     * @since 1.5 First time this was introduced.
     * @return mixed
     */
    public function getLastCommentDate()
    {
        return $this->hasOne(\lispa\amos\comments\models\Comment::className(), ['context_id' => 'id'])
            ->andWhere(['context' => \lispa\amos\discussioni\models\DiscussioniTopic::className()])
            ->max('created_at');
    }
    
    /**
     * @since 1.5 First time this was introduced.
     * @return ActiveQuery
     */
    public function getLastComments()
    {
        return $this->hasOne(\lispa\amos\comments\models\Comment::className(), ['context_id' => 'id'])
            ->andWhere(['context' => \lispa\amos\discussioni\models\DiscussioniTopic::className()])
            ->orderBy(['created_at' => SORT_DESC])->limit(3);
    }
    
    /**
     * @since 1.5 First time this was introduced.
     * @param \lispa\amos\comments\models\Comment $comment
     * @return ActiveQuery
     */
    public function getCommentCreatorUser(Comment $comment)
    {
        return $comment->getCreatedUserProfile();
    }
    
    /**
     * @since 1.5 First time this was introduced.
     * @return array
     */
    public function commentsUsersAvatars()
    {
        $avatars = [];
        /** @var \yii\db\ActiveQuery $query */
        $query = Comment::find()
            ->andWhere(['context_id' => $this->id, 'context' => \lispa\amos\discussioni\models\DiscussioniTopic::className()])
            ->groupBy(['created_by'])
            ->limit(4);
        $listRecord = $query->all();
        foreach ($listRecord as $record) {
            $modelClass = \lispa\amos\admin\AmosAdmin::instance()->createModel('UserProfile');
            $usr = $modelClass::find()->where(['user_id' => $record->created_by])->one();
            if ($usr) {
                $avatars[] = $usr;
            }
        }
        return $avatars;
    }
    
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->titolo;
    }
    
    /**
     * @return string
     */
    public function getDescription($truncate)
    {
        $ret = $this->testo;
        
        if ($truncate) {
            $ret = $this->__shortText($this->testo, 200);
        }
        return $ret;
    }

    
    /**
     * @return array
     */
    public function getStatsToolbar()
    {
        $panels = [];
        $count_comments = 0;
        
        try {
            $panels = parent::getStatsToolbar();
            $filescount =  !is_null($this->discussionsTopicImage) ? $this->getFileCount() - 1 : $this->getFileCount();
            $panels = ArrayHelper::merge($panels, StatsToolbarPanels::getDocumentsPanel($this, $filescount));
            if ($this->isCommentable()) {
                $commentModule = \Yii::$app->getModule('comments');
                if ($commentModule) {
                    /** @var \lispa\amos\comments\AmosComments $commentModule */
                    $count_comments = $commentModule->countComments($this);
                }
                $panels = ArrayHelper::merge($panels, StatsToolbarPanels::getCommentsPanel($this, $count_comments));
            }
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $panels;
    }
    
    /**
     * @return DateTime date begin of publication
     */
    public function getPublicatedFrom()
    {
        return null;
    }
    
    /**
     * @return DateTime date end of publication
     */
    public function getPublicatedAt()
    {
        return null;
    }
    
    /**
     * @return \yii\db\ActiveQuery category of content
     */
    public function getCategory()
    {
        return null;
    }

    /**
     * @return string The url to view of this model
     */
    public function getFullViewUrl()
    {
        return Url::toRoute(["/".$this->getViewUrl(), "id" => $this->id]);
    }

    /**
     * @return mixed
     */
    public function getGrammar()
    {
        return new DiscussionTopicGrammar();
    }

    /**
     * @return array list of statuses that for cwh is validated
     */
    public function getCwhValidationStatuses()
    {
        return [$this->getValidatedStatus()];
    }
}
