<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 */

namespace phpDocumentor\Reflection;

/**
 * Interface for project. Since the definition of a project can be different per factory this interface will be small.
 */
interface Project
{
    /**
     * Returns the name of the project.
     *
     * @return string
     */
    public function getName();
}
