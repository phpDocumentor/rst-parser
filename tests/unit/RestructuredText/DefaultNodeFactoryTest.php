<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use Doctrine\Common\EventManager;
use phpDocumentor\Guides\RestructuredText\Environment;
use phpDocumentor\Guides\RestructuredText\NodeFactory\DefaultNodeFactory;
use phpDocumentor\Guides\RestructuredText\NodeFactory\NodeInstantiator;
use phpDocumentor\Guides\RestructuredText\Nodes\AnchorNode;
use phpDocumentor\Guides\RestructuredText\Nodes\CodeNode;
use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use phpDocumentor\Guides\RestructuredText\Nodes\ListNode;
use phpDocumentor\Guides\RestructuredText\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Nodes\NodeTypes;
use phpDocumentor\Guides\RestructuredText\Nodes\ParagraphNode;
use phpDocumentor\Guides\RestructuredText\Nodes\QuoteNode;
use phpDocumentor\Guides\RestructuredText\Nodes\SeparatorNode;
use phpDocumentor\Guides\RestructuredText\Nodes\SpanNode;
use phpDocumentor\Guides\RestructuredText\Nodes\TableNode;
use phpDocumentor\Guides\RestructuredText\Nodes\TitleNode;
use phpDocumentor\Guides\RestructuredText\Nodes\TocNode;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DefaultNodeFactoryTest extends TestCase
{
    /** @var EventManager */
    private $eventManager;

    public function testCreateDocument() : void
    {
        $returnClass = DocumentNode::class;
        $type        = NodeTypes::DOCUMENT;

        $environment      = $this->createMock(Environment::class);
        $nodeInstantiator = $this->createMock(NodeInstantiator::class);
        $expectedReturn   = $this->createMock($returnClass);

        $nodeInstantiator->expects(self::once())
            ->method('getType')
            ->willReturn($type);

        $nodeInstantiator->expects(self::once())
            ->method('create')
            ->with([$environment])
            ->willReturn($expectedReturn);

        $defaultNodeFactory = $this->createDefaultNodeFactory($nodeInstantiator);

        self::assertSame($expectedReturn, $defaultNodeFactory->createDocumentNode($environment));
    }

    public function testCreateToc() : void
    {
        $returnClass = TocNode::class;
        $type        = NodeTypes::TOC;

        $environment      = $this->createMock(Environment::class);
        $nodeInstantiator = $this->createMock(NodeInstantiator::class);
        $expectedReturn   = $this->createMock($returnClass);

        $nodeInstantiator->expects(self::once())
            ->method('getType')
            ->willReturn($type);

        $nodeInstantiator->expects(self::once())
            ->method('create')
            ->with([$environment, [], []])
            ->willReturn($expectedReturn);

        $defaultNodeFactory = $this->createDefaultNodeFactory($nodeInstantiator);

        self::assertSame($expectedReturn, $defaultNodeFactory->createTocNode($environment, [], []));
    }

    public function testCreateTitle() : void
    {
        $returnClass = TitleNode::class;
        $type        = NodeTypes::TITLE;

        $node             = $this->createMock(Node::class);
        $nodeInstantiator = $this->createMock(NodeInstantiator::class);
        $expectedReturn   = $this->createMock($returnClass);

        $nodeInstantiator->expects(self::once())
            ->method('getType')
            ->willReturn($type);

        $nodeInstantiator->expects(self::once())
            ->method('create')
            ->with([$node, 1, 'test'])
            ->willReturn($expectedReturn);

        $defaultNodeFactory = $this->createDefaultNodeFactory($nodeInstantiator);

        self::assertSame($expectedReturn, $defaultNodeFactory->createTitleNode($node, 1, 'test'));
    }

    public function testCreateSeparator() : void
    {
        $returnClass = SeparatorNode::class;
        $type        = NodeTypes::SEPARATOR;

        $nodeInstantiator = $this->createMock(NodeInstantiator::class);
        $expectedReturn   = $this->createMock($returnClass);

        $nodeInstantiator->expects(self::once())
            ->method('getType')
            ->willReturn($type);

        $nodeInstantiator->expects(self::once())
            ->method('create')
            ->with([1])
            ->willReturn($expectedReturn);

        $defaultNodeFactory = $this->createDefaultNodeFactory($nodeInstantiator);

        self::assertSame($expectedReturn, $defaultNodeFactory->createSeparatorNode(1));
    }

    public function testCreateCode() : void
    {
        $returnClass = CodeNode::class;
        $type        = NodeTypes::CODE;

        $nodeInstantiator = $this->createMock(NodeInstantiator::class);
        $expectedReturn   = $this->createMock($returnClass);

        $nodeInstantiator->expects(self::once())
            ->method('getType')
            ->willReturn($type);

        $nodeInstantiator->expects(self::once())
            ->method('create')
            ->with([[]])
            ->willReturn($expectedReturn);

        $defaultNodeFactory = $this->createDefaultNodeFactory($nodeInstantiator);

        self::assertSame($expectedReturn, $defaultNodeFactory->createCodeNode([]));
    }

    public function testCreateQuote() : void
    {
        $returnClass = QuoteNode::class;
        $type        = NodeTypes::QUOTE;

        $nodeInstantiator = $this->createMock(NodeInstantiator::class);
        $expectedReturn   = $this->createMock($returnClass);

        $nodeInstantiator->expects(self::once())
            ->method('getType')
            ->willReturn($type);

        $documentNode = $this->createMock(DocumentNode::class);

        $nodeInstantiator->expects(self::once())
            ->method('create')
            ->with([$documentNode])
            ->willReturn($expectedReturn);

        $defaultNodeFactory = $this->createDefaultNodeFactory($nodeInstantiator);

        self::assertSame($expectedReturn, $defaultNodeFactory->createQuoteNode($documentNode));
    }

    public function testCreateParagraph() : void
    {
        $returnClass = ParagraphNode::class;
        $type        = NodeTypes::PARAGRAPH;

        $nodeInstantiator = $this->createMock(NodeInstantiator::class);
        $expectedReturn   = $this->createMock($returnClass);

        $nodeInstantiator->expects(self::once())
            ->method('getType')
            ->willReturn($type);

        $parser = $this->createMock(Parser::class);

        $spanNode = new SpanNode($parser, 'test');

        $nodeInstantiator->expects(self::once())
            ->method('create')
            ->with([$spanNode])
            ->willReturn($expectedReturn);

        $defaultNodeFactory = $this->createDefaultNodeFactory($nodeInstantiator);

        self::assertSame($expectedReturn, $defaultNodeFactory->createParagraphNode($spanNode));
    }

    public function testCreateAnchor() : void
    {
        $returnClass = AnchorNode::class;
        $type        = NodeTypes::ANCHOR;

        $nodeInstantiator = $this->createMock(NodeInstantiator::class);
        $expectedReturn   = $this->createMock($returnClass);

        $nodeInstantiator->expects(self::once())
            ->method('getType')
            ->willReturn($type);

        $nodeInstantiator->expects(self::once())
            ->method('create')
            ->with(['test'])
            ->willReturn($expectedReturn);

        $defaultNodeFactory = $this->createDefaultNodeFactory($nodeInstantiator);

        self::assertSame($expectedReturn, $defaultNodeFactory->createAnchorNode('test'));
    }

    public function testCreateList() : void
    {
        $returnClass = ListNode::class;
        $type        = NodeTypes::LIST;

        $nodeInstantiator = $this->createMock(NodeInstantiator::class);
        $expectedReturn   = $this->createMock($returnClass);

        $nodeInstantiator->expects(self::once())
            ->method('getType')
            ->willReturn($type);

        $nodeInstantiator->expects(self::once())
            ->method('create')
            ->with([])
            ->willReturn($expectedReturn);

        $defaultNodeFactory = $this->createDefaultNodeFactory($nodeInstantiator);

        self::assertSame($expectedReturn, $defaultNodeFactory->createListNode());
    }

    public function testCreateTable() : void
    {
        $returnClass = TableNode::class;
        $type        = NodeTypes::TABLE;

        $lineChecker      = $this->createMock(LineChecker::class);
        $nodeInstantiator = $this->createMock(NodeInstantiator::class);
        $expectedReturn   = $this->createMock($returnClass);

        $nodeInstantiator->expects(self::once())
            ->method('getType')
            ->willReturn($type);

        $separatorLineConfig = new Parser\TableSeparatorLineConfig(true, TableNode::TYPE_SIMPLE, [], '=', '=== ===');
        $nodeInstantiator->expects(self::once())
            ->method('create')
            ->with([$separatorLineConfig, TableNode::TYPE_SIMPLE, $lineChecker])
            ->willReturn($expectedReturn);

        $defaultNodeFactory = $this->createDefaultNodeFactory($nodeInstantiator);

        self::assertSame($expectedReturn, $defaultNodeFactory->createTableNode($separatorLineConfig, TableNode::TYPE_SIMPLE, $lineChecker));
    }

    public function testCreateSpan() : void
    {
        $returnClass = SpanNode::class;
        $type        = NodeTypes::SPAN;

        $parser           = $this->createMock(Parser::class);
        $nodeInstantiator = $this->createMock(NodeInstantiator::class);
        $expectedReturn   = $this->createMock($returnClass);

        $nodeInstantiator->expects(self::once())
            ->method('getType')
            ->willReturn($type);

        $nodeInstantiator->expects(self::once())
            ->method('create')
            ->with([$parser, 'test'])
            ->willReturn($expectedReturn);

        $defaultNodeFactory = $this->createDefaultNodeFactory($nodeInstantiator);

        self::assertSame($expectedReturn, $defaultNodeFactory->createSpanNode($parser, 'test'));
    }

    public function testGetNodeInstantiatorThrowsInvalidArgumentException() : void
    {
        $nodeInstantiator = $this->createMock(NodeInstantiator::class);

        $nodeInstantiator->expects(self::once())
            ->method('getType')
            ->willReturn('invalid');

        $defaultNodeFactory = $this->createDefaultNodeFactory($nodeInstantiator);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not find node instantiator of type list');

        $defaultNodeFactory->createListNode();
    }

    protected function setUp() : void
    {
        $this->eventManager = $this->createMock(EventManager::class);
    }

    private function createDefaultNodeFactory(NodeInstantiator $nodeInstantiator) : DefaultNodeFactory
    {
        return new DefaultNodeFactory($this->eventManager, $nodeInstantiator);
    }
}
