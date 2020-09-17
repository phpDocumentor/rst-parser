<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LiteralNestedInDirective;

use phpDocumentor\Guides\RestructuredText\Directives\SubDirective;
use phpDocumentor\Guides\RestructuredText\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser;

class TipDirective extends SubDirective
{
    /**
     * @param string[] $options
     */
    final public function processSub(Parser $parser, ?Node $document, string $variable, string $data, array $options) : ?Node
    {
        return $parser->getNodeFactory()->createWrapperNode($document, '<div class="tip">', '</div>');
    }

    /**
     * Get the directive name
     */
    public function getName() : string
    {
        return 'tip';
    }
}
