<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\widgets\graphics
 * @category   CategoryName
 */

namespace lispa\amos\admin\widgets\graphics;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\assets\ModuleAdminAsset;
use lispa\amos\core\forms\WidgetGraphicsActions;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\widget\WidgetGraphic;
use Yii;
use yii\widgets\Pjax;

class WidgetGraphicMyProfile extends WidgetGraphic
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setCode('USER_PROFILE_GRAPHIC');
        $this->setLabel(AmosAdmin::t('amosadmin', 'Il mio profilo (grafico)'));
        $this->setDescription(AmosAdmin::t('amosadmin', 'Riassume alcune informazioni sul profilo'));
    }

    /**
     * @inheritdoc
     */
    public function getHtml()
    {
        ModuleAdminAsset::register($this->getView());
        $moduleAdmin = \Yii::$app->getModule(AmosAdmin::getModuleName());

        if(!is_null(Yii::$app->getModule('layout'))) {
            $linkProfilo = Html::a(AmosAdmin::t('amosadmin',
                'Completa le informazioni per farti conoscere meglio e sapere dove trovare ciò che ti occorre'), [
                '/admin/user-profile/update',
                'id' => AmosAdmin::instance()->createModel('UserProfile')->findOne(['user_id' => Yii::$app->getUser()->id])->id
            ], ['title' => AmosAdmin::t('amosadmin', 'va al mio profilo')]);
            $linkCompleted = Html::a(AmosAdmin::t('amosadmin',
                'Il tuo profilo è completo. Ricorda di tenerlo aggiornato.'),
                [
                    '/admin/user-profile/view',
                    'id' => AmosAdmin::instance()->createModel('UserProfile')->findOne(['user_id' => Yii::$app->getUser()->id])->id
                ], ['title' => AmosAdmin::t('amosadmin', 'va al mio profilo')]);
            $UserProfile = AmosAdmin::instance()->createModel('UserProfile')->findOne(['user_id' => Yii::$app->getUser()->id]);

            $isCompleted = $UserProfile->getCompletamentoProfilo() == '100';
            $toRefreshSectionId = 'myProfileGraphicWidget';

            $html = '
            <div class="grid-item">
            <div class="box-widget myprofile">
                <div class="box-widget-toolbar dark-toolbar row nom">
                    <h2 class="box-widget-title col-xs-10 nop">' . AmosAdmin::t('amosadmin',
                    'Il mio profilo') . '</h2>';

            if(isset($moduleAdmin) && !$moduleAdmin->hideWidgetGraphicsActions) {
                $html .= WidgetGraphicsActions::widget([
                    'widget' => $this,
                    'tClassName' => '\Yii',
                    'actionRoute' => '/admin/user-profile/create',
                    'toRefreshSectionId' => $toRefreshSectionId
                ]);
            }

            $html .= '
                </div>
                <section><h2 class="sr-only">' . AmosAdmin::t('amosadmin', 'il mio profilo') . '</h2>';

            Pjax::begin(['id' => $toRefreshSectionId]);
            Yii::$app->imageUtility->methodGetImageUrl = "getAvatarUrl";
            $img = Html::img($UserProfile->getAvatarUrl(), [
                'class' => Yii::$app->imageUtility->getRoundImage($UserProfile)['class'],
                'style' => "margin-left: " . Yii::$app->imageUtility->getRoundImage($UserProfile)['margin-left'] . "%; margin-top: " . Yii::$app->imageUtility->getRoundImage($UserProfile)['margin-top'] . "%;",
                'alt' => $UserProfile->getNomeCognome()
            ]);

            $html .= '
                    <div role="listbox">
                        <div class="widget-listbox-option row list-items" role="option">
                            <article class="col-xs-12 nop">
                                <div class="icon-admin-wgt">
                                    <span class="pull-left">' .
                Html::a(
                    $img,
                    "/admin/user-profile/view?id=" . $UserProfile->id,
                    ['title' => AmosAdmin::t('amosadmin', 'va al mio profilo'), 'class' => 'container-square-img-sm']
                ) .
                '</span>
                                </div>
                                <div class="text-admin-wgt">
                                    <h3 class="box-widget-subtitle">' . AmosAdmin::t('amosadmin',
                    'Le mie informazioni') . '</h3>
                                    <p class="box-widget-text">' . ($isCompleted ? $linkCompleted : $linkProfilo) . '</p>
                                    </div>
                                 <div class="clearfix"></div>
                            </article>
                        </div>
                    </div>';

            Pjax::end();

            $html .= '
                </section>
            </div>
            </div>
        ';

            return $html;
        } else {
            return $this->getHtmlOld();
        }
    }

    /**
     * @inheritdoc
     */
    public function getHtmlOld()
    {

        $linkProfilo = Html::a(AmosAdmin::t('amosadmin',
            'Completa le informazioni per farti conoscere meglio e sapere dove trovare ciò che ti occorre'), [
            '/admin/user-profile/update',
            'id' => AmosAdmin::instance()->createModel('UserProfile')->findOne(['user_id' => Yii::$app->getUser()->id])->id
        ], ['title' => AmosAdmin::t('amosadmin', 'va al mio profilo')]);
        $linkCompleted = Html::a(AmosAdmin::t('amosadmin', 'Il tuo profilo è completo. Ricorda di tenerlo aggiornato.'),
            [
                '/admin/user-profile/view',
                'id' => AmosAdmin::instance()->createModel('UserProfile')->findOne(['user_id' => Yii::$app->getUser()->id])->id
            ], ['title' => AmosAdmin::t('amosadmin', 'va al mio profilo')]);
        $UserProfile = AmosAdmin::instance()->createModel('UserProfile')->findOne(['user_id' => Yii::$app->getUser()->id]);

        $isCompleted = $UserProfile->getCompletamentoProfilo() == '100';
        $toRefreshSectionId = 'myProfileGraphicWidget';

        $html = '
            <div class="box-widget">
                <div class="box-widget-toolbar row nom">
                    <h2 class="box-widget-title col-xs-10 nop">' . AmosAdmin::t('amosadmin',
                'Il mio profilo') . '</h2>';

        $html .= WidgetGraphicsActions::widget([
            'widget' => $this,
            'tClassName' => '\Yii',
            'actionRoute' => '/admin/user-profile/create',
            'toRefreshSectionId' => $toRefreshSectionId
        ]);

        $html .= '
                </div>
                <section><h2 class="sr-only">'.AmosAdmin::t('amosadmin', 'il mio profilo').'</h2>';

        Pjax::begin(['id' => $toRefreshSectionId]);
        Yii::$app->imageUtility->methodGetImageUrl = "getAvatarUrl";
        $img =Html::img($UserProfile->getAvatarUrl(), [
            'class' => Yii::$app->imageUtility->getRoundImage($UserProfile)['class'],
            'style' => "margin-left: " . Yii::$app->imageUtility->getRoundImage($UserProfile)['margin-left'] . "%; margin-top: " . Yii::$app->imageUtility->getRoundImage($UserProfile)['margin-top'] . "%;",
            'alt' => $UserProfile->getNomeCognome()
        ]);

        $html .= '
                    <div role="listbox">
                        <div class="widget-listbox-option row list-items" role="option">
                            <article class="col-xs-12 nop">
                                <div class="col-xs-4 nopl">
                                    <span class="pull-left">' .
            Html::a(
                $img,
                "/admin/user-profile/view?id=" . $UserProfile->id,
                ['title' => AmosAdmin::t('amosadmin', 'va al mio profilo'), 'class' => 'container-round-img-md ']
            ) .
            '</span>
                                </div>
                                <div class="col-xs-8 nopl">
                                    <h3 class="box-widget-subtitle">' . AmosAdmin::t('amosadmin',
                'Le mie informazioni') . '</h3>
                                    <p class="box-widget-text">' . ($isCompleted ? $linkCompleted : $linkProfilo) . '</p>
                                    </div>
                                
                                 <div class="clearfix"></div>
                            </article>
                        </div>
                    </div>';

        Pjax::end();

        $html .= '
                </section>
            </div>
        ';

        return $html;
    }
}