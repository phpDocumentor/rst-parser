<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

use phpDocumentor\Guides\RestructuredText\Builder\Documents;
use phpDocumentor\Guides\RestructuredText\Builder\ParseQueue;
use phpDocumentor\Guides\RestructuredText\Builder\ParseQueueProcessor;
use phpDocumentor\Guides\RestructuredText\ErrorManager;
use phpDocumentor\Guides\RestructuredText\Kernel;
use phpDocumentor\Guides\RestructuredText\Meta\Metas;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function sys_get_temp_dir;
use function touch;

class ParseQueueProcessorTest extends TestCase
{
    /** @var Kernel|MockObject */
    private $kernel;

    /** @var ErrorManager|MockObject */
    private $errorManager;

    /** @var Metas|MockObject */
    private $metas;

    /** @var Documents|MockObject */
    private $documents;

    /** @var string */
    private $directory;

    /** @var string */
    private $targetDirectory;

    /** @var string */
    private $fileExtension;

    /** @var ParseQueueProcessor */
    private $parseQueueProcessor;

    public function testProcess() : void
    {
        touch($this->directory . '/file.rst');

        $parseQueue = new ParseQueue();
        $parseQueue->addFile('file', true);

        $this->documents->expects(self::once())
            ->method('addDocument')
            ->with('file');

        $this->kernel->expects(self::once())
            ->method('postParse');

        $this->metas->expects(self::once())
            ->method('set');

        $this->parseQueueProcessor->process($parseQueue);
    }

    protected function setUp() : void
    {
        $this->kernel          = $this->createMock(Kernel::class);
        $this->errorManager    = $this->createMock(ErrorManager::class);
        $this->metas           = $this->createMock(Metas::class);
        $this->documents       = $this->createMock(Documents::class);
        $this->directory       = sys_get_temp_dir();
        $this->targetDirectory = '/target';
        $this->fileExtension   = 'rst';

        $this->parseQueueProcessor = new ParseQueueProcessor(
            $this->kernel,
            $this->errorManager,
            $this->metas,
            $this->documents,
            $this->directory,
            $this->targetDirectory,
            $this->fileExtension
        );
    }
}
