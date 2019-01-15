<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 */

namespace phpDocumentor\Reflection;

/**
 * Interface for Api Elements
 */
interface Element
{
    /**
     * Returns the Fqsen of the element.
     *
     * @return Fqsen
     */
    public function getFqsen();

    /**
     * Returns the name of the element.
     *
     * @return string
     */
    public function getName();
}