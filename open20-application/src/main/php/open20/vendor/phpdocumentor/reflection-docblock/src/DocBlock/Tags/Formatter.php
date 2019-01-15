<?php
/**
 *
 *
 */

namespace phpDocumentor\Reflection\DocBlock\Tags;

use phpDocumentor\Reflection\DocBlock\Tag;

interface Formatter
{
    /**
     * Formats a tag into a string representation according to a specific format, such as Markdown.
     *
     * @param Tag $tag
     *
     * @return string
     */
    public function format(Tag $tag);
}
