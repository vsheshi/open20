<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\community
 * @category   CategoryName
 */

namespace lispa\amos\community\widgets;


use lispa\amos\admin\base\ConfigurationManager;
use lispa\amos\community\AmosCommunity;
use lispa\amos\community\models\Community;
use lispa\amos\core\forms\ContextMenuWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Modal;

class CommunityCardWidget extends Widget
{
    /**
     * @var Community $model
     */
    public $model;

    /**
     * @var bool|false $imgStyleDisableHorizontalFix - do not use class full-height and dynamic margin calculation in case of horizontal img
     */
    public $imgStyleDisableHorizontalFix = false;

    /**
     * @var bool|true $onlyLogo displays only the img (logo) of community, no card tooltip
     */
    public $onlyLogo = true;

    public $enableLink = true;

    public $absoluteUrl = false;

    /**
     * widget initialization
     */
    public function init()
    {
        parent::init();

        if (is_null($this->model)) {
            throw new \Exception(AmosCommunity::t('amoscommunity', 'Missing model'));
        }
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $model = $this->model;
        $html = '';

        $url = '/img/img_default.jpg';
        if (!is_null($model->communityLogo)) {
            if($this->absoluteUrl) {
                $url = $model->communityLogo->getWebUrl('square_small', false, true);
            } else {
                $url = $model->communityLogo->getUrl('square_small', false, true);
            }
        }
        Yii::$app->imageUtility->methodGetImageUrl = 'getUrl';
        $roundImage = Yii::$app->imageUtility->getRoundImage($model->communityLogo);

        $class = $roundImage['class'];
        if($this->absoluteUrl){
            if($class == 'full-width'){
                $style = "width: 100%; height: auto; margin-top:". $roundImage['margin-top']  . "%;";
            } elseif ($class == 'full-height') {
                $style = "height: 100%; width: auto; margin-left: " . $roundImage['margin-left'] . "%;";
            } else {
                $style = " width: 100%; height: auto;";
            }
            $htmlOptions = [
                'style' => $style,
                'alt' => $model->getAttributeLabel('communityLogo')
            ];
        } else {
            $htmlOptions = [
                'class' => !empty($class) ? $class : 'square-img',
                'style' => ((!$this->imgStyleDisableHorizontalFix) ? "margin-left: " . $roundImage['margin-left'] . "%;" :  "") . "margin-top: " . $roundImage['margin-top'] . "%;",
                'alt' => $model->getAttributeLabel('communityLogo')
            ];
        }
        $htmlTag = Html::img(Yii::$app->getUrlManager()->createAbsoluteUrl($url), $htmlOptions);
        $img = ($this->absoluteUrl) ? $htmlTag : Html::tag('div',$htmlTag, ['class' => 'container-round-img-sm']);
        if($this->onlyLogo){
            $link = null;
            if($this->enableLink){
                $link = '/community/community/view?id='.$model->id;
                if($this->absoluteUrl) {
                    $link = Yii::$app->getUrlManager()->createAbsoluteUrl($link);
                }
            }
            $html .=  Html::a( $img, $link, ['title' => $model->name]);
        }else {
            $img = Html::tag('div', $img, ['class' => 'container-round-img-sm']);
            $modals = JoinCommunityWidget::widget([
                'model' => $this->model,
                'onlyModals' => true
            ]);
            $html = $modals . Html::a(
                    $img,
                    null,
                    [
                        'data' => [
                            'toggle' => 'tooltip',
                            'html' => true,
                            'placement' => 'right',
                            'delay' => ['show' => 100, 'hide' => 5000],
                            'trigger' => 'hover',
                            'template' => '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner" style="background-color:transparent;min-width: 200px;"></div></div>'
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

        $viewUrl = "/community/community/view?id=" . $model->id;
        $url = '/img/img_default.jpg';
        if (!is_null($model->communityLogo)) {
            $url = $model->communityLogo->getUrl('square_small', false, true);
        }
        Yii::$app->imageUtility->methodGetImageUrl = 'getUrl';
        $roundImage = Yii::$app->imageUtility->getRoundImage($model->communityLogo);
        $logo = Html::img($url, [
            'class' => $roundImage['class'],
            'style' => ((!$this->imgStyleDisableHorizontalFix) ? "margin-left: " . $roundImage['margin-left'] . "%;" :  "") . "margin-top: " . $roundImage['margin-top'] . "%;",
            'alt' => $model->getAttributeLabel('communityLogo')
        ]);
        $tooltip = '<div class="icon-view"><div class="card-container col-xs-12 nop">' .
            ContextMenuWidget::widget([
                'model' => $model,
                'actionModify' => "/community/community/update?id=" . $model->id,
                'optionsModify' => [
                    'class' => 'community-modify',
                    'data-target' => (($model->status == Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED)? '#visibleOnEditPopup'.$model->id : ''),
                    'data-toggle' => 'modal'
                ],
                'mainDivClasses' => '',
                'disableDelete' => true
            ])
            . '<div class="icon-header grow-pict">
                         <div class="container-round-img">' .
        Html::a($logo, $viewUrl, ['title' => $model->name])  . '</div>';
        $tooltip .= JoinCommunityWidget::widget([
            'model' => $model,
            'divClassBtnContainer' => 'under-img',
            'onlyButton' => true
        ]);
        $tooltip .= '</div><div class="icon-body">';
        $newsWidget = \lispa\amos\notificationmanager\forms\NewsWidget::widget([
            'model' => $model,
        ]);
        $tooltip .= $newsWidget . '<h3>' . Html::a($model->name, $viewUrl, ['title' => $model->name]) . '</h3>';


        if ($model->validated_once) {
            $icons = '';
            $color = "grey";
            $title = AmosCommunity::t('amoscommunity', 'Edit in progress');
            if($model->status == Community::COMMUNITY_WORKFLOW_STATUS_VALIDATED){
                $color = "green";
                $title = AmosCommunity::t('amoscommunity', 'Validated');
            }
            $statusIcon = AmosIcons::show('check-all', [
                'class' => 'am-2 ',
                'style' => 'color: ' . $color,
                'title' => $title
            ]);
            $icons .= $statusIcon;
            $tooltip .= Html::tag('div', $icons);
        }

        $tooltip .= '<p>' .  AmosCommunity::t('amoscommunity', 'Access type: ') . AmosCommunity::t('amoscommunity', $model->getCommunityTypeName()) . '</p>';

        $tooltip .= '</div></div></div>';

        return $tooltip;
    }
}