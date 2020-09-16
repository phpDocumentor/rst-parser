<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

use phpDocumentor\Guides\RestructuredText\Builder\Documents;
use phpDocumentor\Guides\RestructuredText\Meta\MetaEntry;
use phpDocumentor\Guides\RestructuredText\Meta\Metas;
use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class DocumentsTest extends TestCase
{
    /** @var Filesystem|MockObject */
    private $filesystem;

    /** @var Metas|MockObject */
    private $metas;

    /** @var Documents */
    private $documents;

    public function testGetAll() : void
    {
        $document1 = $this->createMock(DocumentNode::class);
        $document2 = $this->createMock(DocumentNode::class);

        $this->documents->addDocument('document1', $document1);
        $this->documents->addDocument('document2', $document2);

        $expected = [
            'document1' => $document1,
            'document2' => $document2,
        ];

        self::assertSame($expected, $this->documents->getAll());
    }

    public function testHasDocument() : void
    {
        self::assertFalse($this->documents->hasDocument('document'));

        $document = $this->createMock(DocumentNode::class);

        $this->documents->addDocument('document', $document);

        self::assertTrue($this->documents->hasDocument('document'));
    }

    public function testRender() : void
    {
        $document = $this->createMock(DocumentNode::class);

        $this->documents->addDocument('document', $document);

        $metaEntry = $this->createMock(MetaEntry::class);

        $this->metas->expects(self::once())
            ->method('get')
            ->with('document')
            ->willReturn($metaEntry);

        $metaEntry->expects(self::once())
            ->method('getUrl')
            ->willReturn('url');

        $this->filesystem->expects(self::once())
            ->method('mkdir')
            ->with('/target', 0755);

        $document->expects(self::once())
            ->method('renderDocument')
            ->willReturn('rendered document');

        $this->filesystem->expects(self::once())
            ->method('dumpFile')
            ->with('/target/url', 'rendered document');

        $this->documents->render('/target');
    }

    protected function setUp() : void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->metas      = $this->createMock(Metas::class);

        $this->documents = new Documents(
            $this->filesystem,
            $this->metas
        );
    }
}
