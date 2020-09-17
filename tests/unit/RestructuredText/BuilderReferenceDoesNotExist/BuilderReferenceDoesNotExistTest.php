<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\BuilderReferenceDoesNotExist;

use phpDocumentor\Guides\RestructuredText\Builder;
use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\Kernel;
use phpDocumentor\Guides\RestructuredText\BaseBuilderTest;

class BuilderReferenceDoesNotExistTest extends BaseBuilderTest
{
    /** @var Configuration */
    private $configuration;

    protected function setUp() : void
    {
        $this->configuration = new Configuration();
        $this->configuration->setUseCachedMetas(false);
        $this->configuration->abortOnError(false);
        $this->configuration->setIgnoreInvalidReferences(false);

        $this->builder = new Builder(new Kernel($this->configuration));
    }

    public function testReferenceDoesNotExist() : void
    {
        $this->builder->build($this->sourceFile(), $this->targetFile());

        $contents = $this->getFileContents($this->targetFile('subdir/index.html'));

        self::assertContains('<p>Test link 1 to</p>', $contents);
        self::assertContains('<p>Test link 2 to</p>', $contents);
    }

    protected function getFixturesDirectory() : string
    {
        return 'BuilderReferenceDoesNotExist';
    }
}
