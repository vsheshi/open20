<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace SebastianBergmann\CodeCoverage\Report\Xml;

use TheSeer\Tokenizer\NamespaceUri;
use TheSeer\Tokenizer\Tokenizer;
use TheSeer\Tokenizer\XMLSerializer;

class Source
{
    /** @var \DOMElement */
    private $context;

    /**
     * @param \DOMElement $context
     */
    public function __construct(\DOMElement $context)
    {
        $this->context = $context;
    }

    /**
     * @param string $source
     */
    public function setSourceCode(string $source)
    {
        $context = $this->context;

        $tokens = (new Tokenizer())->parse($source);
        $srcDom = (new XMLSerializer(new NamespaceUri($context->namespaceURI)))->toDom($tokens);

        $context->parentNode->replaceChild(
            $context->ownerDocument->importNode($srcDom->documentElement, true),
            $context
        );
    }
}
