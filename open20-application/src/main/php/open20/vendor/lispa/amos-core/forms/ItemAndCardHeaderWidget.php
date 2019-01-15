<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core\forms
 * @category   CategoryName
 */

namespace lispa\amos\core\forms;

use lispa\amos\admin\models\UserProfile;
use lispa\amos\admin\widgets\UserCardWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\core\module\BaseAmosModule;
use lispa\amos\core\record\Record;
use yii\base\Widget;

/**
 * Class ItemAndCardHeaderWidget
 *
 * Widget to display the header in list view, icon view, item view and card view.
 * The interaction menu has three default buttons: favourite, signal and share.
 * If you want to enable only a certain group of buttons you must set interactionMenuButtons array with only the enabled buttons.
 * If you want to disable only a certain group of buttons you must set interactionMenuButtonsHide array with only the disabled buttons.
 *
 * @package lispa\amos\core\forms
 */
class ItemAndCardHeaderWidget extends Widget
{
    /**
     * @var string $layout Widget view
     */
    public $layout = "@vendor/lispa/amos-core/forms/views/widgets/item_and_card_header_widget.php";

    /**
     * @var Record $model
     */
    private $_model = null;

    /**
     * @var bool $_publicationDateField Model field that contains the publication date.
     */
    private $_publicationDateField = null;

    /**
     * @var bool $_publicationDateNotPresent If true skip the render of the publication date.
     */
    private $_publicationDateNotPresent = false;

    /**
     * @var bool $_hideInteractionMenu If true hide all interaction menu. Default to false.
     */
    private $_hideInteractionMenu = false;

    /**
     * @var array $_interactionMenuButtons List of the enabled buttons in the interaction menu. If not set, the default buttons will be displayed.
     */
    private $_interactionMenuButtons = [];

    /**
     * @var array $_interactionMenuButtonsHide List of the disabled buttons in the interaction menu. If not set, the default buttons will be displayed.
     */
    private $_interactionMenuButtonsHide = [];

    /**
     * @var UserProfile $_contentCreator The object that contains the profile of the content creator.
     */
    private $_contentCreator = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (is_null($this->_model)) {
            throw new \Exception(BaseAmosModule::t('amoscore', 'Model mancante'));
        }

        if (!$this->getPublicationDateNotPresent() && !$this->isHideInteractionMenu() && (is_null($this->_publicationDateField)
            || !is_string($this->_publicationDateField) || !strlen($this->_publicationDateField))) {
            throw new \Exception(BaseAmosModule::t('amoscore',
                'Variabile contenente il nome del campo della data di pubblicazione del contenuto mancante o non settata correttamente'));
        }

        $this->_contentCreator = \lispa\amos\admin\AmosAdmin::instance()->createModel('UserProfile')->findOne(['user_id' => $this->getModel()->created_by]);
    }

    /**
     * @return string
     */
    public function getPublicationDateNotPresent()
    {
        return $this->_publicationDateNotPresent;
    }

    /**
     * @param string $publicationDateNotPresent
     */
    public function setPublicationDateNotPresent($publicationDateNotPresent)
    {
        $this->_publicationDateNotPresent = $publicationDateNotPresent;
    }

    /**
     * @return bool
     */
    public function isHideInteractionMenu()
    {
        return $this->_hideInteractionMenu;
    }

    /**
     * @param bool $hideInteractionMenu
     */
    public function setHideInteractionMenu($hideInteractionMenu)
    {
        $this->_hideInteractionMenu = $hideInteractionMenu;
    }

    /**
     * @return Record
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param Record $model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->renderFile($this->getLayout(),
                [
                'contentCreatorAvatar' => $this->makeContentCreatorAvatar(),
                'contentCreatorNameSurname' => $this->retrieveUserNameAndSurname(),
                'contentCreatorModel' => $this->retrieveCreatorModel(),
                'hideInteractionMenu' => $this->isHideInteractionMenu(),
                'interactionMenuButtons' => $this->getInteractionMenuButtons(),
                'interactionMenuButtonsHide' => $this->getInteractionMenuButtonsHide(),
                'publicatonDate' => $this->makePublicationDate(),
                'model' => $this->getModel()
        ]);
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * This method create the HTML to show the content creator avatar.
     * @return string
     */
    private function makeContentCreatorAvatar()
    {
        $html           = '';
        $contentCreator = $this->getContentCreator();
        if ($contentCreator) {
            $moduleAdmin = \Yii::$app->getModule('admin');
            if (!empty($moduleAdmin)) {
                if (property_exists(UserCardWidget::className(), 'enableLink')) {
                    $html = UserCardWidget::widget(['model' => $contentCreator, 'enableLink' => true]);
                } else {
                    $html = UserCardWidget::widget(['model' => $contentCreator]);
                }
            } else {
                $html .= Html::a(
                        Html::img($contentCreator->getAvatarUrl(),
                            [
                            'width' => '50',
                            'class' => 'avatar'
                        ]), "/admin/user-profile/view?id=".$contentCreator->id
                );
            }
        }
        return $html;
    }

    /**
     * @return UserProfile
     */
    public function getContentCreator()
    {
        return $this->_contentCreator;
    }

    /**
     * This method creates a string that contains the name and surname of the user whose ID is contained in the parameter.
     * @return string
     */
    private function retrieveUserNameAndSurname()
    {
        $nameSurname = BaseAmosModule::t('amoscore', 'Utente Cancellato');
        $userProfile = \lispa\amos\admin\AmosAdmin::instance()->createModel('UserProfile')->findOne(['user_id' => $this->getModel()->created_by]);
        if (!is_null($userProfile)) {
            $nameSurname = $userProfile->__toString();
        }
        return $nameSurname;
    }

    /**
     * This the user profile model of the creator.
     * @return string
     */
    private function retrieveCreatorModel()
    {
        return $userProfile = \lispa\amos\admin\AmosAdmin::instance()->createModel('UserProfile')->findOne(['user_id' => $this->getModel()->created_by]);
    }

    /**
     * @return array
     */
    public function getInteractionMenuButtons()
    {
        return $this->_interactionMenuButtons;
    }

    /**
     * @param array $interactionMenuButtons
     */
    public function setInteractionMenuButtons($interactionMenuButtons)
    {
        $this->_interactionMenuButtons = $interactionMenuButtons;
    }

    /**
     * @return array
     */
    public function getInteractionMenuButtonsHide()
    {
        return $this->_interactionMenuButtonsHide;
    }

    /**
     * @param array $interactionMenuButtonsHide
     */
    public function setInteractionMenuButtonsHide($interactionMenuButtonsHide)
    {
        $this->_interactionMenuButtonsHide = $interactionMenuButtonsHide;
    }

    /**
     * This method format the publication date field of the model ad a date. If the publication date is not present in the model returns an empty string.
     * @return string
     */
    private function makePublicationDate()
    {
        $publicationDate = '';
        if (!$this->getPublicationDateNotPresent()) {
            $publicationDateModelField = $this->getPublicationDateField();
            $publicationDate           = \Yii::$app->getFormatter()->asDate($this->getModel()->{$publicationDateModelField});
        }
        return $publicationDate;
    }

    /**
     * @return string
     */
    public function getPublicationDateField()
    {
        return $this->_publicationDateField;
    }

    /**
     * @param string $publicationDateField
     */
    public function setPublicationDateField($publicationDateField)
    {
        $this->_publicationDateField = $publicationDateField;
    }
}
