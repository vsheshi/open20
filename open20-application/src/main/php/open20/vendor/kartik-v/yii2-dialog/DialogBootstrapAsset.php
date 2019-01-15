<?php

/**
 * @package   yii2-dialog
 * @version   1.0.3
 */

namespace kartik\dialog;
use kartik\base\PluginAssetBundle;

/**
 * Asset bundle for Bootstrap 3 Dialog
 *
 * @since 1.0
 */
class DialogBootstrapAsset extends PluginAssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath('@bower/bootstrap3-dialog');
        $this->setupAssets('js', ['dist/js/bootstrap-dialog']);
        $this->setupAssets('css', ['dist/css/bootstrap-dialog']);
        parent::init();
    }
}
