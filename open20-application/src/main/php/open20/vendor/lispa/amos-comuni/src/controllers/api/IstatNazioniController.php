<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comuni
 * @category   CategoryName
 */

namespace lispa\amos\comuni\controllers\api;

/**
 * This is the class for REST controller "IstatNazioniController".
 */

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class IstatNazioniController extends \yii\rest\ActiveController
{
    public $modelClass = 'lispa\amos\comuni\models\IstatNazioni';
}
