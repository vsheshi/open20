<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\wizflow
 * @category   CategoryName
 */

namespace lispa\amos\wizflow;

/**
 * Interface WizflowModelInterface
 * Interface that must be implemented by all models used by the wizflow manager component
 * @package lispa\amos\wizflow
 */
interface WizflowModelInterface
{
    /**
     * Returns a string description of the model. This string is used to display user choices
     * @return string description of the model attributes
     */
    public function summary();
}
