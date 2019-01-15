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
 * Interface MyActivitiesModelsInterface
 * @package lispa\amos\myactivities\basic
 */
interface MyActivitiesModelsInterface
{
    public function getCreatedAt();
    public function getUpdatedAt();
    public function getCreatorNameSurname();
    public function getSearchString();
    public function getWrappedObject();
}