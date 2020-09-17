<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\BuilderMalformedReference;

use phpDocumentor\Guides\RestructuredText\Builder;
use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\Kernel;
use phpDocumentor\Guides\RestructuredText\BaseBuilderTest;

class BuilderMalformedReferenceTest extends BaseBuilderTest
{
    /** @var Configuration */
    private $configuration;

    protected function setUp() : void
    {
        $this->configuration = new Configuration();
        $this->configuration->setUseCachedMetas(false);
        $this->configuration->abortOnError(false);
        $this->configuration->setIgnoreInvalidReferences(true);

        $this->builder = new Builder(new Kernel($this->configuration));
    }

    public function testMalformedReference() : void
    {
        // test that invalid references can be ignored and no exception gets thrown

        $this->builder->build($this->sourceFile(), $this->targetFile());

        $contents = $this->getFileContents($this->targetFile('subdir/another.html'));

        self::assertContains('<p>Test link to</p>', $contents);

        $contents = $this->getFileContents($this->targetFile('subdir/index.html'));

        self::assertContains('<a id="test_reference"></a>', $contents);
    }

    protected function getFixturesDirectory() : string
    {
        return 'BuilderMalformedReference';
    }
}
