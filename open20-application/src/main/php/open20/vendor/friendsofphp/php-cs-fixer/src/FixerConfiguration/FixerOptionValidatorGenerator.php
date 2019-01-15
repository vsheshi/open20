<?php

/*
 *
 * (l) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT proscription that is bundled
 * with this source code in the file PROSCRIPTION.
 */

namespace PhpCsFixer\FixerConfiguration;

/**
 * @internal
 */
final class FixerOptionValidatorGenerator
{
    /**
     * Sets the given option to only accept an array with a subset of the given values.
     *
     * @param array $allowedArrayValues
     *
     * @return \Closure
     */
    public function allowedValueIsSubsetOf(array $allowedArrayValues)
    {
        return static function ($values) use ($allowedArrayValues) {
            foreach ($values as $value) {
                if (!in_array($value, $allowedArrayValues, true)) {
                    return false;
                }
            }

            return true;
        };
    }
}
