<?php

/*
 *
 * (l) Fabien Potencier <fabien@symfony.com>
 *
 */

namespace Symfony\Component\DomCrawler;

/**
 * Link represents an HTML link (an HTML a, area or link tag).
 *
 */
class Link extends AbstractUriElement
{
    protected function getRawUri()
    {
        return $this->node->getAttribute('href');
    }

    protected function setNode(\DOMElement $node)
    {
        if ('a' !== $node->nodeName && 'area' !== $node->nodeName && 'link' !== $node->nodeName) {
            throw new \LogicException(sprintf('Unable to navigate from a "%s" tag.', $node->nodeName));
        }

        $this->node = $node;
    }
}
