<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use phpDocumentor\Guides\RestructuredText\Renderers\DocumentNodeRenderer as BaseDocumentRender;
use phpDocumentor\Guides\RestructuredText\Renderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\RestructuredText\Renderers\NodeRenderer;
use phpDocumentor\Guides\RestructuredText\Templates\TemplateRenderer;
use Gajus\Dindent\Indenter;

class DocumentNodeRenderer implements NodeRenderer, FullDocumentNodeRenderer
{
    /** @var DocumentNode */
    private $document;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(DocumentNode $document, TemplateRenderer $templateRenderer)
    {
        $this->document         = $document;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        return (new BaseDocumentRender($this->document))->render();
    }

    public function renderDocument() : string
    {
        $headerNodes = '';

        foreach ($this->document->getHeaderNodes() as $node) {
            $headerNodes .= $node->render() . "\n";
        }

        $html = $this->templateRenderer->render('document.html.twig', [
            'headerNodes' => $headerNodes,
            'bodyNodes' => $this->render(),
        ]);

        if ($this->document->getConfiguration()->getIndentHTML()) {
            return $this->indentHTML($html);
        }

        return $html;
    }

    private function indentHTML(string $html) : string
    {
        return (new Indenter())->indent($html);
    }
}
