<?php
/**
 * @package   yii2-export
 * @version   1.2.9
 *
 * Export Submission Form
 *
 */
use yii\helpers\Html;

/**
 * @var array  $options
 * @var string $exportTypeParam
 * @var string $exportType
 * @var string $exportRequestParam
 * @var string $exportColsParam
 * @var string $colselFlagParam
 * @var string $columnSelectorEnabled
 */
echo Html::beginForm('', 'post', $options);
echo Html::hiddenInput($exportTypeParam, $exportType);
echo Html::hiddenInput($exportRequestParam, 1);
echo Html::hiddenInput($exportColsParam, '');
echo Html::hiddenInput($colselFlagParam, $columnSelectorEnabled);
echo Html::endForm();
