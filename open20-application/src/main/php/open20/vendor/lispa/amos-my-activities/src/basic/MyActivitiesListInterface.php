<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\myactivities\basic
 * @category   CategoryName
 */

namespace lispa\amos\myactivities\basic;

/**
 * Interface MyActivitiesListInterface
 * @package lispa\amos\myactivities\basic
 */
interface MyActivitiesListInterface
{
    public function getMyActivitiesList();
    public function setMyActivitiesList($myActivitiesList);
    public function addModelSet($listModel);
}