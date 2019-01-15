<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\PhpDocumentor;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\MethodTag as LegacyMethodTag;

/**
 *
 * @internal
 */
final class LegacyClassTagRetriever implements MethodTagRetrieverInterface
{
    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return LegacyMethodTag[]
     */
    public function getTagList(\ReflectionClass $reflectionClass)
    {
        $phpdoc = new DocBlock($reflectionClass->getDocComment());

        return $phpdoc->getTagsByName('method');
    }
}
