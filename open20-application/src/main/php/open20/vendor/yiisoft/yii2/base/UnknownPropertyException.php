<?php
/**
 */

namespace yii\base;

/**
 * UnknownPropertyException represents an exception caused by accessing unknown object properties.
 *
 * @since 2.0
 */
class UnknownPropertyException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Unknown Property';
    }
}
