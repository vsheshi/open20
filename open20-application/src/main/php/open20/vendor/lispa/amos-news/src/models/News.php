<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\news\models
 * @category   CategoryName
 */

namespace lispa\amos\news\models;

use lispa\amos\attachments\behaviors\FileBehavior;
use lispa\amos\attachments\models\File;
use lispa\amos\comments\models\CommentInterface;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\interfaces\ContentModelInterface;
use lispa\amos\core\interfaces\ViewModelInterface;
use lispa\amos\core\views\toolbars\StatsToolbarPanels;
use lispa\amos\news\AmosNews;
use lispa\amos\news\i18n\grammar\NewsGrammar;
use lispa\amos\news\widgets\icons\WidgetIconNewsDashboard;
use lispa\amos\notificationmanager\behaviors\NotifyBehavior;
use lispa\amos\upload\models\FilemanagerMediafile;
use lispa\amos\workflow\behaviors\WorkflowLogFunctionsBehavior;
use pendalf89\filemanager\behaviors\MediafileBehavior;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use Yii;
use yii\base\Exception;
use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\log\Logger;

/**
 * Class News
 *
 * @method \cornernote\workflow\manager\components\WorkflowDbSource getWorkflowSource()
 * @method \yii\db\ActiveQuery hasOneFile($attribute = 'file', $sort = 'id')
 * @method \yii\db\ActiveQuery hasMultipleFiles($attribute = 'file', $sort = 'id')
 * @method string|null getRegolaPubblicazione()
 * @method array getTargets()
 *
 * @package lispa\amos\news\models
 */
class News extends \lispa\amos\news\models\base\News implements ContentModelInterface, CommentInterface, ViewModelInterface
{
    // Workflow ID
    const NEWS_WORKFLOW = 'NewsWorkflow';

    // Workflow states IDS
    const NEWS_WORKFLOW_STATUS_BOZZA = 'NewsWorkflow/BOZZA';
    const NEWS_WORKFLOW_STATUS_DAVALIDARE = 'NewsWorkflow/DAVALIDARE';
    const NEWS_WORKFLOW_STATUS_VALIDATO = 'NewsWorkflow/VALIDATO';
    const NEWS_WORKFLOW_STATUS_NONVALIDATO = 'NewsWorkflow/NONVALIDATO';

    /**
     * Create news scenario
     */
    const SCENARIO_CREATE = 'news_create';

    /**
     * All the scenarios listed below are for the wizard.
     */
    const SCENARIO_INTRODUCTION = 'scenario_introduction';
    const SCENARIO_DETAILS = 'scenario_details';
    const SCENARIO_PUBLICATION = 'scenario_publication';
    const SCENARIO_SUMMARY = 'scenario_summary';

    const SCENARIO_DETAILS_HIDE_PUBBLICATION_DATE = 'scenario_details_hide_pubblication_date';
    const SCENARIO_CREATE_HIDE_PUBBLICATION_DATE = 'scenario_create_hide_pubblication_date';
    const SCENARIO_UPDATE_HIDE_PUBBLICATION_DATE = 'scenario_update_hide_pubblication_date';





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
     * @var string $distance Distanza
     */
    public $distance;

    /**
     * @var string $destinatari_pubblicazione Destinatari pubblicazione
     */
    public $destinatari_pubblicazione;

    /**
     * @var string $destinatari_notifiche Destinatari notifiche
     */
    public $destinatari_notifiche;

    /**
     * @var File $newsImage
     */
    private $newsImage;

    /**
     * @var File[] $attachments
     */
    private $attachments;

    /**
     * @var File[] $attachmentsForItemView
     */
    private $attachmentsForItemView;

    /**
     */
    public function init()
    {
        parent::init();

        if ($this->isNewRecord) {
            $this->status = $this->getWorkflowSource()->getWorkflow(self::NEWS_WORKFLOW)->getInitialStatusId();

            $moduleNews = \Yii::$app->getModule(AmosNews::getModuleName());
            if(!is_null($moduleNews)){
                if($moduleNews->hidePubblicationDate) {
                    // the news will be visible forever
                    $this->data_rimozione =  '9999-12-31';
                }
                $this->data_pubblicazione = date("Y-m-d");
            }
        }
    }

    public function afterFind()
    {
        parent::afterFind();
    }

    /**
     * Getter for $this->newsImage;
     *
     */
    public function getNewsImage()
    {
        if (empty($this->newsImage)) {
            $this->newsImage = $this->hasOneFile('newsImage')->one();
        }
        return $this->newsImage;
    }

    /**
     * @param $image
     */
    public function setNewsImage($image)
    {
        $this->newsImage = $image;
    }

    /**
     * @return string
     */
    public function getNewsImageUrl($size = 'original', $protected = true, $url = '/img/img_default.jpg')
    {
        $newsImage = $this->getNewsImage();
        if (!is_null($newsImage)) {
            if ($protected) {
                $url = $newsImage->getUrl($size);
            } else {
                $url = $newsImage->getWebUrl($size);
            }
        }
        return $url;
    }

    /**
     * Getter for $this->attachments;
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getAttachments()
    {
        if (empty($this->attachments)) {
            $query = $this->hasMultipleFiles('attachments');
            $query->multiple = false;
            $this->attachments = $query->one();
        }
        return $this->attachments;
    }

    /**
     * @param $attachments
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAttachmentsForItemView()
    {

        if (empty($this->attachmentsForItemView)) {
            $query = $this->hasMultipleFiles('attachments');
            $query->multiple = false;
            $this->attachmentsForItemView = $query->all();
        }
        return $this->attachmentsForItemView;
    }

    /**
     * @param $attachments
     */
    public function setAttachmentsForItemView($attachments)
    {
        $this->attachmentsForItemView = $attachments;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $parentScenarios = parent::scenarios();
        $scenarios = ArrayHelper::merge(
            $parentScenarios,
            [
                self::SCENARIO_CREATE => $parentScenarios[self::SCENARIO_DEFAULT]
            ]
        );
        $scenarios[self::SCENARIO_INTRODUCTION] = [];
        $scenarios[self::SCENARIO_DETAILS] = [
            'titolo',
            'sottotitolo',
            'descrizione_breve',
            'descrizione',
            'news_categorie_id',
            'data_pubblicazione',
            'data_rimozione',
            'newsImage',
            'comments_enabled',
            'status',
        ];
        $scenarios[self::SCENARIO_PUBLICATION] = [
            'destinatari_pubblicazione',
            'destinatari_notifiche'
        ];
        $scenarios[self::SCENARIO_SUMMARY] = [
            'status'
        ];
        /** @var AmosNews $newsModule */
        $newsModule = Yii::$app->getModule(AmosNews::getModuleName());
        if ($newsModule->params['site_publish_enabled']) {
            $scenarios[self::SCENARIO_DETAILS][] = 'primo_piano';
        }
        if ($newsModule->params['site_featured_enabled']) {
            $scenarios[self::SCENARIO_DETAILS][] = 'in_evidenza';
        }
        $scenarios[self::SCENARIO_DETAILS_HIDE_PUBBLICATION_DATE] = $scenarios[self::SCENARIO_DETAILS];
        $scenarios[self::SCENARIO_CREATE_HIDE_PUBBLICATION_DATE] = $scenarios[self::SCENARIO_CREATE];

        return $scenarios;
    }

    /**
     */
    public function rules()
    {
        $requiredArray = AmosNews::getInstance()->newsRequiredFields;

        $rules =  ArrayHelper::merge(parent::rules(), [
            [$requiredArray, 'required'],
            [['slug', 'destinatari_pubblicazione', 'destinatari_notifiche'], 'safe'],
            [['attachments'], 'file', 'maxFiles' => 0],
            [['newsImage'], 'file', 'extensions' => 'jpeg, jpg, png, gif'],
        ]);

        if($this->scenario != self::SCENARIO_DETAILS_HIDE_PUBBLICATION_DATE && $this->scenario != self::SCENARIO_CREATE_HIDE_PUBBLICATION_DATE && $this->scenario != self::SCENARIO_UPDATE_HIDE_PUBBLICATION_DATE) {
            $rules = ArrayHelper::merge($rules, [
                [['data_pubblicazione', 'data_rimozione'], 'required'],
                ['data_pubblicazione', 'compare', 'compareAttribute' => 'data_rimozione', 'operator' => '<='],
                ['data_rimozione', 'compare', 'compareAttribute' => 'data_pubblicazione', 'operator' => '>='],
                ['data_pubblicazione', 'checkDate'],
            ]);
        }
        return $rules;
    }

    /**
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
            'mediafile' => [
                'class' => MediafileBehavior::className(),
                'name' => get_class($this),
                'attributes' => [
                    'immagine',
                ],
            ],
            'workflow' => [
                'class' => SimpleWorkflowBehavior::className(),
                'defaultWorkflowId' => self::NEWS_WORKFLOW,
                'propagateErrorsToModel' => true
            ],
            'NotifyBehavior' => [
                'class' => NotifyBehavior::className(),
                'conditions' => [],
            ],
            'fileBehavior' => [
                'class' => FileBehavior::className()
            ],
            'WorkflowLogFunctionsBehavior' => [
                'class' => WorkflowLogFunctionsBehavior::className(),
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
                'newsImage' => AmosNews::t('amosnews', 'News image')
            ]
        );
    }

    /**
     * @see\lispa\amos\core\record\Record::representingColumn() or more info.
     */
    public function representingColumn()
    {
        return [
            'titolo'
        ];
    }

    /**
     * @return string
     * TODO Serve ancora
     */
    public function getImageUrl($dimension = 'original')
    {
        $url = '/img/img_default.jpg';
        if ($this->immagine) {
            $mediafile = FilemanagerMediafile::findOne($this->immagine);
            if ($mediafile) {
                $url = $mediafile->getThumbUrl($dimension);
            }
        }
        return $url;
    }

    /**
     * @inheritdoc
     */
    public function getGridViewColumns()
    {
        return [
            'immagine' => [
                'label' => AmosNews::t('amosnews', 'Immagine'),
                'format' => 'html',
                'value' => function ($model) {
                    $url = '/img/img_default.jpg';
                    if (!is_null($model->newsImage)) {
                        $url = $model->newsImage->getUrl('original');
                    }
                    return Html::img($url, [
                        'class' => 'gridview-image'
                    ]);
                },
                'headerOptions' => [
                    'id' => AmosNews::t('amosnews', 'immagine'),
                ],
                'contentOptions' => [
                    'headers' => AmosNews::t('amosnews', 'immagine'),
                ]
            ],
            'titolo' => [
                'attribute' => 'titolo',
                'headerOptions' => [
                    'id' => AmosNews::t('amosnews', 'titolo'),
                ],
                'contentOptions' => [
                    'headers' => AmosNews::t('amosnews', 'titolo'),
                ]
            ],
            'created_by' => [
                'attribute' => 'createdUserProfile',
                'label' => AmosNews::t('amosnews', 'Creato da'),
                'headerOptions' => [
                    'id' => AmosNews::t('amosnews', 'creato da'),
                ],
                'contentOptions' => [
                    'headers' => AmosNews::t('amosnews', 'creato da'),
                ]
            ],
            'data_pubblicazione' => [
                'attribute' => 'data_pubblicazione',
                'format' => 'date',
                'headerOptions' => [
                    'id' => AmosNews::t('amosnews', 'data pubblicazione'),
                ],
                'contentOptions' => [
                    'headers' => AmosNews::t('amosnews', 'data pubblicazione'),
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getViewUrl()
    {
        return "news/news/view";
    }

    /**
     * @inheritdoc
     */
    public function getToValidateStatus()
    {
        return self::NEWS_WORKFLOW_STATUS_DAVALIDARE;
    }

    /**
     * @inheritdoc
     */
    public function getValidatedStatus()
    {
        return self::NEWS_WORKFLOW_STATUS_VALIDATO;
    }

    /**
     * @inheritdoc
     */
    public function getDraftStatus()
    {
        return self::NEWS_WORKFLOW_STATUS_BOZZA;
    }

    /**
     * @inheritdoc
     */
    public function getValidatorRole()
    {
        return 'VALIDATORE_NEWS';
    }

    public function getPluginWidgetClassname()
    {
        return WidgetIconNewsDashboard::className();
    }

    /**
     * @inheritdoc
     */
    public function isCommentable()
    {
        return $this->comments_enabled;
    }

    /**
     * Verifica la presenza dell'immagine.
     *
     * @param   $url
     *
     * @return  bool
     */
    protected function image_exists($url)
    {
        try {
            if (getimagesize(Yii::$app->getBasePath() . '/web' . $url)) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->titolo;
    }

    /**
     * @inheritdoc
     */
    public function getDescription($truncate)
    {
        $ret = $this->descrizione;

        if ($truncate) {
            $ret = $this->__shortText($this->descrizione, 200);
        }
        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function getStatsToolbar()
    {
        $panels = [];
        $count_comments = 0;

        try {
            $panels = parent::getStatsToolbar();
            $filescount = !is_null($this->newsImage) ? $this->getFileCount() - 1 : $this->getFileCount();
            $panels = ArrayHelper::merge($panels, StatsToolbarPanels::getDocumentsPanel($this, $filescount));
            if ($this->isCommentable()) {
                $commentModule = \Yii::$app->getModule('comments');
                if ($commentModule) {
                    /** @var \lispa\amos\comments\AmosComments $commentModule */
                    $count_comments = $commentModule->countComments($this);
                }
                $panels = ArrayHelper::merge($panels, StatsToolbarPanels::getCommentsPanel($this, $count_comments));
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $panels;
    }

    /**
     * @inheritdoc
     */
    public function getPublicatedFrom()
    {
        return $this->data_pubblicazione;
    }

    /**
     * @inheritdoc
     */
    public function getPublicatedAt()
    {
        return $this->data_rimozione;
    }

    /**
     * This is the relation between the news and the related category.
     * Return an ActiveQuery related to NewsCategorie model.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(\lispa\amos\news\models\NewsCategorie::className(), ['id' => 'news_categorie_id']);
    }

    /**
     * @return string The url to view of this model
     */
    public function getFullViewUrl()
    {
        return Url::toRoute(["/" . $this->getViewUrl(), "id" => $this->id]);
    }

    /**
     * @return mixed
     */
    public function getGrammar()
    {
        return new NewsGrammar();
    }

    /**
     * @return array list of statuses that for cwh is validated
     */
    public function getCwhValidationStatuses()
    {
        return [$this->getValidatedStatus()];
    }

    public function setDetailScenario(){
        $moduleNews = \Yii::$app->getModule(AmosNews::getModuleName());
        if($moduleNews->hidePubblicationDate == true){
            $this->setScenario(News::SCENARIO_DETAILS_HIDE_PUBBLICATION_DATE);
        }
        else {
            $this->setScenario(News::SCENARIO_DETAILS);
        }
    }
}
