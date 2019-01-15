<?php
namespace lispa\amos\core\forms\editors\socialShareWidget;

use yii\base\Widget;
use yii\helpers\Html;
use ymaker\social\share\widgets\SocialShare;

class SocialShareWidget extends SocialShare
{

//    public $containerOptions = [
//        'tag' => 'div',
//        'class' => 'container-social-share'
//    ];
//    public $linkContainerOptions = [
//        'tag' => 'div',
//        'class' => 'share-wrap-button'
//    ];

    public $wrapperTag = 'div';
    public $wrapperOptions = ['class' => 'container-social-share'];

    public $linkWrapperTag = 'div';
    public $linkWrapperOptions = [ 'class' => 'share-wrap-button'];


    public $enableModalShare = true;


    public function init()
    {
        parent::init();
        if(empty($this->imageUrl)){
            $this->imageUrl = \yii\helpers\Url::to(\Yii::$app->params['platform']['backendUrl']. "/img/img_default.jpg");
        }
    }

    /**
     *
     */
    public function run()
    {
        $this->renderModal();
        parent::run();
    }

    /**
     * Render Modal for sharing
     */
    public function renderModal(){
        if($this->enableModalShare) {
            $view = $this->getView();
            $view->registerJs("$(document).ready(function() {
            $('.social-network').click(function(e) {
                e.preventDefault();
                window.open($(this).attr('href'), 'share', 'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
                return false;
            });
        });");
        }
    }


}