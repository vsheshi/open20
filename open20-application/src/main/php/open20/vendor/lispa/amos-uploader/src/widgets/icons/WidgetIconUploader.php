<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package     lispa\amos\uploader\widgets\icons
 * @category   CategoryName
 */

namespace lispa\amos\uploader\widgets\icons;

use lispa\amos\core\widget\WidgetIcon;
use lispa\amos\uploader\Module;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconUploader
 * @package lispa\amos\uploader\widgets\icons
 */
class WidgetIconUploader extends WidgetIcon
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setLabel(Module::tHtml('uploader', 'Uploader'));
        $this->setDescription(Module::t('uploader', 'Upload files of great size'));
        $this->setIcon('linentita');
        $this->setUrl(['/uploader/upload/index']);
        $this->setCode('UPLOADER');
        $this->setModuleName('uploader');
        $this->setNamespace(__CLASS__);
        $this->setClassSpan(ArrayHelper::merge($this->getClassSpan(), [
            'bk-backgroundIcon',
            'color-darkGrey'
        ]));
    }
}