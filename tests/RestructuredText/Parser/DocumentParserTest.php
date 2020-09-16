<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use Doctrine\Common\EventManager;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Environment;
use phpDocumentor\Guides\RestructuredText\NodeFactory\NodeFactory;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use Exception;
use PHPUnit\Framework\TestCase;

class DocumentParserTest extends TestCase
{
    public function testErrorWhenDirectiveThrowsException() : void
    {
        $parser             = $this->createMock(Parser::class);
        $environment        = $this->createMock(Environment::class);
        $nodeFactory        = $this->createMock(NodeFactory::class);
        $eventManager       = $this->createMock(EventManager::class);
        $codeBlockDirective = $this->createMock(Directive::class);

        $docParser = new DocumentParser(
            $parser,
            $environment,
            $nodeFactory,
            $eventManager,
            ['code-block' => $codeBlockDirective],
            true,
            __DIR__
        );

        $codeBlockDirective->expects(self::once())
            ->method('process')
            ->willThrowException(new Exception('Invalid something something!'));
        $codeBlockDirective->expects(self::once())
            ->method('getName')
            ->willReturn('code-block-name');

        $environment->expects(self::once())
            ->method('addError')
            ->with('Error while processing "code-block-name" directive: Invalid something something!');

        $docParser->parse('.. code-block:: php');
    }
}
