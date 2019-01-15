<?php

/*
 *
 * (l) Fabien Potencier <fabien@symfony.com>
 *
 */

namespace Symfony\Component\Yaml\Tag;

/**
 */
final class TaggedValue
{
    private $tag;
    private $value;

    /**
     * @param string $tag
     * @param mixed  $value
     */
    public function __construct($tag, $value)
    {
        $this->tag = $tag;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
