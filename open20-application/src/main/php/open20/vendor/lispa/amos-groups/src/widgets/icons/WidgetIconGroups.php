<?php

namespace lispa\amos\groups\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconGroups
 * @package lispa\amos\groups\widgets\icons
 */
class WidgetIconGroups extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setLabel(\Yii::t('amosgroups', 'Groups'));
        $this->setDescription(Yii::t('amosgroups', 'Groups'));
        $this->setIcon('group');
        $this->setIconFramework('am');
        $this->setUrl(Yii::$app->urlManager->createUrl(['/groups/groups']));
        $this->setModuleName('partecipanti');
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-primary'
        ]));
    }
}
