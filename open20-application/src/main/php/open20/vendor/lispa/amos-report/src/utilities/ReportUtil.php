<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\report
 * @category   CategoryName
 */

namespace lispa\amos\report\utilities;

use lispa\amos\report\AmosReport;

/**
 * Class ReportUtil
 * @package lispa\amos\report\utilities
 */
class ReportUtil
{

    public static function translateArrayValues($arrayValues)
    {
        $translatedArrayValues = [];
        foreach ($arrayValues as $key => $title) {
            $translatedArrayValues[$key] = AmosReport::t('amosreport', $title);
        }
        return $translatedArrayValues;
    }
}