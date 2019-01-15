<?php

namespace lispa\amos\cwh\controllers;


use lispa\amos\core\controllers\BaseController;
use lispa\amos\cwh\AmosCwh;
use lispa\amos\cwh\base\ModelConfig;
use lispa\amos\cwh\helpers\ContentHelper;
use lispa\amos\cwh\helpers\NetworkHelper;
use lispa\amos\cwh\models\CwhConfig;
use lispa\amos\cwh\models\CwhConfigContents;
use lispa\amos\cwh\utility\CwhUtil;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;

/**
 * Class ConfigurationController
 * @package lispa\amos\cwh\controllers
 *
 */
class ConfigurationController extends BaseController
{
    const CONTENTS_DATA_CACHE_KEY = 'ContentsData';
    const NETWORKS_DATA_CACHE_KEY = 'NetworksData';
    const LAST_PROCESS_DATETIME_CACHE_KEY = 'CwhTime';

    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public function init() {

        $this->setModelObj(new ModelConfig());

        parent::init();
        $this->setUpLayout();
        // custom initialization code goes here
    }

    public function behaviors()
    {

        $rolesEnabled = [
            'AMMINISTRATORE_CWH'
        ];

        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['wizard', 'contents', 'network'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['wizard', 'contents', 'network', 'regenerate-view'],
                        'roles' => $rolesEnabled,
                    ],
                ],
                'denyCallback' => function ($rule) {
                    if (\Yii::$app->getUser()->isGuest) {
                        \Yii::$app->getSession()->addFlash('warning',
                            AmosCwh::t('wizard', 'La sessione è scaduta, effettua il login'));
                        \Yii::$app->getUser()->loginRequired();
                    }
                    throw new ForbiddenHttpException(
                        AmosCwh::t('wizard',
                            'Non sei autorizzato a visualizzare questa pagina, per accedere a questa funzionalità abilitare uno dei seguenti ruoli all\'utente {rolesEnabled}.',
                            [
                                'rolesEnabled' => $rule
                            ]
                        )
                    );
                }
            ],
        ];
    }

    public function actionContent($id = null)
    {
        $Content = CwhConfigContents::findOne($id);

        if (!($Content)) {
            $Content = new CwhConfigContents();
            $Content->load(\Yii::$app->getRequest()->getQueryParams(), '');
            if($Content->classname){
                if(class_exists($Content->classname)){
                    $modelObject = \Yii::createObject($Content->classname);
                    if(!$Content->status_attribute) {
                        if ($modelObject->hasProperty('status')) {
                            $Content->status_attribute = 'status';
                            if ($modelObject->status) {
                                $Content->status_value = $modelObject->status;
                            }
                        }
                    }
                }
            }
        }

        if (\Yii::$app->getRequest()->getIsPost()) {
            $Content->load(\Yii::$app->getRequest()->post());
            if ($Content->save(false)) {
                return $this->redirect(['content',
                    'id' => $Content->id
                ]);
            }
        }

        return $this->render('contents', [
            'Content' => $Content,
            'Statuses' => $Content->statuses,
        ]);
    }

    public function actionNetwork($id = null)
    {
        $Network = CwhConfig::findOne($id);

        if (!($Network)) {
            $Network = new CwhConfig();
            $Network->load(\Yii::$app->getRequest()->getQueryParams(), '');
        }

        if (\Yii::$app->getRequest()->getIsPost()) {
            $Network->load(\Yii::$app->getRequest()->post());
            if ($Network->save(false)) {
                return $this->redirect(['network',
                    'id' => $Network->id
                ]);
            }
        }

        return $this->render('network', [
            'Network' => $Network,
        ]);
    }

    /**
     * Regenerates Cwh nodi view
     *
     */
    public function regenerateView()
    {
        try {
            CwhUtil::createCwhView();
        } catch (\Exception $e) {
            Yii::$app->getSession()->addFlash('warning', AmosCwh::t('amoscwh', 'Vista non creata correttamente. COD. ERROR: ' . $e->getMessage()));
        }
        Yii::$app->getSession()->addFlash('success', AmosCwh::t('amoscwh', 'Vista creata correttamente.'));

    }

    /**
     * @param bool $regenerateView
     * @return string
     */
    public function actionWizard($regenerateView = false)
    {
        Url::remember();

        $this->setUpLayout('main');

        if($regenerateView) {
            $this->regenerateView();
        }

        if (($post = \Yii::$app->getRequest()->post())) {
            if (isset($post['delete_cache'])) {
                set_time_limit(60);
                \Yii::$app->getCache()->delete(self::CONTENTS_DATA_CACHE_KEY);
                \Yii::$app->getCache()->delete(self::NETWORKS_DATA_CACHE_KEY);
                \Yii::$app->getCache()->delete(self::LAST_PROCESS_DATETIME_CACHE_KEY);
            }
        }

        $ContentsData = \Yii::$app->getCache()->getOrSet(self::CONTENTS_DATA_CACHE_KEY,
            function () {
                return ContentHelper::getEntities();
            });

        $NetworksData = \Yii::$app->getCache()->getOrSet(self::NETWORKS_DATA_CACHE_KEY,
            function () {
                return NetworkHelper::getEntities();
            });

        $time = time();
        $lastProcessDateTime = \Yii::$app->getCache()->getOrSet(self::LAST_PROCESS_DATETIME_CACHE_KEY,
            function () use ($time) {
                return $time;
            });

        $ContentsDataProvider = new ArrayDataProvider([
            'allModels' => $ContentsData,
        ]);

        $NetworksDataProvider = new ArrayDataProvider([
            'allModels' => $NetworksData,
        ]);

        return $this->render('wizard', [
            'networksDataProvider' => $NetworksDataProvider,
            'contentsDataProvider' => $ContentsDataProvider,
            'lastProcessDateTime' => $lastProcessDateTime,
        ]);
    }

}