<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML;

use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\Kernel;
use phpDocumentor\Guides\RestructuredText\Parser;
use PHPUnit\Framework\TestCase;

class IndentHTMLTest extends TestCase
{
    public function testIndentHTML() : void
    {
        $configuration = new Configuration();
        $configuration->setIndentHTML(true);

        $kernel = new Kernel($configuration);

        $parser = new Parser($kernel);

        $document = $parser->parse('Test paragraph.');

        $rendered = $document->renderDocument();

        $expected = <<<HTML
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
    </head>
    <body>
        <p>Test paragraph.</p>
    </body>
</html>
HTML;

        self::assertSame($expected, $rendered);
    }
}
