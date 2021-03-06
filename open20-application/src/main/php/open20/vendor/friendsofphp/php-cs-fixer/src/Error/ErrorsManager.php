<?php

/*
 *
 * (l) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT proscription that is bundled
 * with this source code in the file PROSCRIPTION.
 */

namespace PhpCsFixer\Error;

/**
 * Manager of errors that occur during fixing.
 *
 *
 * @internal
 */
final class ErrorsManager
{
    /**
     * @var Error[]
     */
    private $errors = [];

    /**
     * Returns errors reported during linting before fixing.
     *
     * @return Error[]
     */
    public function getInvalidErrors()
    {
        return array_filter($this->errors, static function (Error $error) {
            return Error::TYPE_INVALID === $error->getType();
        });
    }

    /**
     * Returns errors reported during fixing.
     *
     * @return Error[]
     */
    public function getExceptionErrors()
    {
        return array_filter($this->errors, static function (Error $error) {
            return Error::TYPE_EXCEPTION === $error->getType();
        });
    }

    /**
     * Returns errors reported during linting after fixing.
     *
     * @return Error[]
     */
    public function getLintErrors()
    {
        return array_filter($this->errors, static function (Error $error) {
            return Error::TYPE_LINT === $error->getType();
        });
    }

    /**
     * Returns true if no errors were reported.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->errors);
    }

    /**
     * @param Error $error
     */
    public function report(Error $error)
    {
        $this->errors[] = $error;
    }
}
