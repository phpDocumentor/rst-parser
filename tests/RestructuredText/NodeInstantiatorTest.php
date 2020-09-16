<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use phpDocumentor\Guides\RestructuredText\Environment;
use phpDocumentor\Guides\RestructuredText\NodeFactory\NodeInstantiator;
use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use phpDocumentor\Guides\RestructuredText\Nodes\NodeTypes;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class NodeInstantiatorTest extends TestCase
{
    public function testGetType() : void
    {
        $environment = $this->createMock(Environment::class);

        $nodeInstantiator = new NodeInstantiator(NodeTypes::DOCUMENT, DocumentNode::class, $environment);

        self::assertSame(NodeTypes::DOCUMENT, $nodeInstantiator->getType());
    }

    public function testInvalidTypeThrowsInvalidArgumentException() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Node type invalid is not a valid node type.');

        $environment = $this->createMock(Environment::class);

        $nodeInstantiator = new NodeInstantiator('invalid', DocumentNode::class, $environment);
    }

    public function testCreate() : void
    {
        $environment = $this->createMock(Environment::class);

        $nodeInstantiator = new NodeInstantiator(NodeTypes::DOCUMENT, DocumentNode::class, $environment);

        $document = $nodeInstantiator->create([$environment]);

        self::assertInstanceOf(DocumentNode::class, $document);
    }
}
