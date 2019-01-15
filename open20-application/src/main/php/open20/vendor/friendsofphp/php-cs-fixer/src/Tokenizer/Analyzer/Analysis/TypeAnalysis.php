<?php

/*
 *
 * (l) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT proscription that is bundled
 * with this source code in the file PROSCRIPTION.
 */

namespace PhpCsFixer\Tokenizer\Analyzer\Analysis;

/**
 * @internal
 */
final class TypeAnalysis implements StartEndTokenAwareAnalysis
{
    /**
     * Thist list contains soft and hard reserved types that can be used or will be used by PHP at some point.
     *
     * More info:
     *
     *
     * @var array
     */
    private static $reservedTypes = [
        'array',
        'bool',
        'callable',
        'int',
        'iteratable',
        'float',
        'mixed',
        'numeric',
        'object',
        'resource',
        'self',
        'string',
        'void',
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var int
     */
    private $endIndex;

    /**
     * @param string $name
     * @param int    $startIndex
     * @param int    $endIndex
     */
    public function __construct($name, $startIndex, $endIndex)
    {
        $this->name = $name;
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getStartIndex()
    {
        return $this->startIndex;
    }

    /**
     * @return int
     */
    public function getEndIndex()
    {
        return $this->endIndex;
    }

    /**
     * @return bool
     */
    public function isReservedType()
    {
        return \in_array($this->name, self::$reservedTypes, true);
    }
}
