<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\admin\widgets
 * @category   CategoryName
 */

namespace lispa\amos\admin\widgets;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\base\ConfigurationManager;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use Yii;
use yii\bootstrap\Widget;

/**
 * Class UserCardWidget
 * @package lispa\amos\admin\widgets
 */
class UserCardWidget extends Widget
{
    /**
     * @var UserProfile $model
     */
    public $model;

    public $onlyAvatar = true;
    public $absoluteUrl = false;
    public $avatarXS = false;
    public $enableLink = true;
    public $containerAdditionalClass = '';
    public $avatarDimension = 'square_small';
    
    /**
     * @var AmosAdmin $adminModule
     */
    private $adminModule = null;
    
    /**
     * widget initialization
     */
    public function init()
    {
        parent::init();
        
        if (is_null($this->model)) {
            throw new \Exception(AmosAdmin::t('amosadmin', 'Missing model'));
        }
        $this->adminModule = Yii::$app->getModule('admin');
    }
    
    /**
     * @return mixed
     */
    public function run()
    {
        $html = '';

        if ($this->absoluteUrl) {
            $url = $this->model->getAvatarWebUrl($this->avatarDimension);
        } else {
            $url = $this->model->getAvatarUrl($this->avatarDimension);
        }
        $model = $this->model;
        $roundImage = Yii::$app->imageUtility->getRoundImage($model);

        if($this->absoluteUrl){
            $class = $roundImage['class'];
            if($class == 'full-width'){
                $style = "width: 100%; height: auto; margin-top:". $roundImage['margin-top']  . "%;";
            } elseif ($class == 'full-height') {
                $style = "height: 100%; width: auto; margin-left: " . $roundImage['margin-left'] . "%;";
            } else {
                $style = " width: 100%; height: auto;";
            }
            $htmlOptions = [
                'style' => $style,
                'alt' => $model->getNomeCognome()
            ];
        } else {
            $htmlOptions = [
                'class' => $roundImage['class'],
                'style' => "margin-left: " . $roundImage['margin-left'] . "%; margin-top: " . $roundImage['margin-top'] . "%;",
                'alt' => $model->getNomeCognome()
            ];
        }

        $htmlTag = Html::img($url, $htmlOptions);
        $img = ($this->absoluteUrl) ? $htmlTag : Html::tag('div',$htmlTag,
            ['class' => 'container-round-img-'. (($this->avatarXS) ? 'xs' : 'sm') .' '. $this->containerAdditionalClass]); 

        if($this->onlyAvatar){
            $link = null;
            if($this->enableLink){
                $link = '/admin/user-profile/view?id='.$model->id;
                if($this->absoluteUrl) {
                    $link = Yii::$app->getUrlManager()->createAbsoluteUrl($link);
                }
            }
            $html .=  Html::a(
                    $img,
                    $link,
                    [
                        'title' => $model->getNomeCognome()
                    ]);
        }else {
            $modals = \lispa\amos\admin\widgets\ConnectToUserWidget::widget([
                'model' => $this->model,
                'onlyModals' => true
            ]);

            $html = $modals . Html::a(
                    $img
                    , null,
                    [
                        'data' => [
                            'toggle' => 'tooltip',
                            'html' => true,
                            'placement' => 'right',
                            'delay' => ['show' => 100, 'hide' => 5000],
                            'trigger' => 'hover',
                            'template' => '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="background-color:transparent"></div></div>'
                        ],
                        'title' => $this->getHtmlTooltip(),
                        'style' => 'border-color:transparent;'
                    ]
                );
        }
        return $html;
    }
    
    private function getHtmlTooltip()
    {
        $model = $this->model;
        
        $nomeCognome = '';
        if ($this->adminModule->confManager->isVisibleBox('box_informazioni_base', ConfigurationManager::VIEW_TYPE_VIEW)) {
            if ($this->adminModule->confManager->isVisibleField('nome', ConfigurationManager::VIEW_TYPE_VIEW)) {
                $nomeCognome .= $model->nome;
            }
            if ($this->adminModule->confManager->isVisibleField('cognome', ConfigurationManager::VIEW_TYPE_VIEW)) {
                $nomeCognome .= ' ' . $model->cognome;
            }
        }
        
        $viewUrl = "/admin/user-profile/view?id=" . $model->id;
        $url = $model->getAvatarUrl('original', [
            'class' => 'img-responsive'
        ]);
        $roundImage = Yii::$app->imageUtility->getRoundImage($model);
        $logoOptions = [
            'class' => $roundImage['class'],
            'style' => "margin-left: " . $roundImage['margin-left'] . "%; margin-top: " . $roundImage['margin-top'] . "%;",
        ];
        $logoLinkOptions = [];
        $options = [];
        if (strlen($nomeCognome)) {
            $logoOptions['alt'] = $nomeCognome;
            $logoLinkOptions['title'] = $nomeCognome;
            $options['title'] = $nomeCognome;
        }
        $logo = Html::img($url, $logoOptions);
        Yii::$app->imageUtility->methodGetImageUrl = 'getAvatarUrl';
        $tooltip = '<div class="icon-view"><div class="card-container col-xs-12 nop">' .
            ContextMenuWidget::widget([
                'model' => $model,
                'actionModify' => "/admin/user-profile/update?id=" . $model->id,
                'disableDelete' => true
            ])
            . '<div class="icon-header grow-pict">
                         <div class="container-round-img">' .
            Html::a($logo, $viewUrl, $logoLinkOptions) . '</div>';
        if (Yii::$app->user->id != $model->user_id) {
            $tooltip .= \lispa\amos\admin\widgets\ConnectToUserWidget::widget([
                'model' => $model,
                'divClassBtnContainer' => 'under-img',
                'onlyButton' => true
            ]);
        }
        $tooltip .= '</div><div class="icon-body">';
        $newsWidget = \lispa\amos\notificationmanager\forms\NewsWidget::widget([
            'model' => $model,
        ]);
        $tooltip .= $newsWidget . '<h3>' . Html::a($nomeCognome, $viewUrl, $options) . '</h3>';
        
        
        if ($model->validato_almeno_una_volta) {
            $icons = '';
            $color = "grey";
            $title = AmosAdmin::t('amosadmin', 'Profile Active');
            if ($model->status == \lispa\amos\admin\models\UserProfile::USERPROFILE_WORKFLOW_STATUS_VALIDATED) {
                $color = "green";
                $title = AmosAdmin::t('amosadmin', 'Profile Validated');
            }
            //TODO replace check-all with cockade
            $statusIcon = AmosIcons::show('check-all', [
                'class' => 'am-2 ',
                'style' => 'color: ' . $color,
                'title' => $title
            ]);
            $icons .= $statusIcon;
            $facilitatorUserIds = Yii::$app->getAuthManager()->getUserIdsByRole("FACILITATOR");
            if (in_array($model->user_id, $facilitatorUserIds)) {
                //TODO replace account with man dressing tie and jacket
                $facilitatorIcon = AmosIcons::show('account', [
                    'class' => 'am-2',
                    'style' => 'color: green',
                    'title' => AmosAdmin::t('amosadmin', 'Facilitator')
                ]);
                $icons .= $facilitatorIcon;
            }
            $tooltip .= Html::tag('div', $icons);
        }
        if (Yii::$app->user->can('ADMIN') && $model->user->email) {
            $tooltip .= '<p>' .
                AmosIcons::show('email')
                . '<span>' . $model->user->email . '</span>' .
                '</p>';
        }
        
        if (
            ($this->adminModule->confManager->isVisibleBox('box_prevalent_partnership', ConfigurationManager::VIEW_TYPE_VIEW)) &&
            ($this->adminModule->confManager->isVisibleField('prevalent_partnership_id', ConfigurationManager::VIEW_TYPE_VIEW))
        ) {
            $tooltip .= '<p>' . (!is_null($model->prevalentPartnership) ? $model->prevalentPartnership->name : AmosAdmin::t('amosadmin', 'Prevalent partnership not specified')) . '</p>';
        }
        
        $tooltip .= '</div></div></div>';
        
        return $tooltip;
    }
}
