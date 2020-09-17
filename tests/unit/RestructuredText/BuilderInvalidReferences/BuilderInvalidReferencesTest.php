<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\BuilderInvalidReferences;

use phpDocumentor\Guides\RestructuredText\Builder;
use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\Kernel;
use phpDocumentor\Guides\RestructuredText\BaseBuilderTest;
use Throwable;

class BuilderInvalidReferencesTest extends BaseBuilderTest
{
    /** @var Configuration */
    private $configuration;

    protected function setUp() : void
    {
        $this->configuration = new Configuration();
        $this->configuration->setUseCachedMetas(false);

        $this->builder = new Builder(new Kernel($this->configuration));
    }

    public function testInvalidReference() : void
    {
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage('Found invalid reference "does_not_exist" in file "index"');

        $this->builder->build($this->sourceFile(), $this->targetFile());
    }

    public function testInvalidReferenceIgnored() : void
    {
        $this->configuration->setIgnoreInvalidReferences(true);

        $this->builder->build($this->sourceFile(), $this->targetFile());

        $contents = $this->getFileContents($this->targetFile('index.html'));

        self::assertContains('<p>Test unresolved reference</p>', $contents);
    }

    protected function getFixturesDirectory() : string
    {
        return 'BuilderInvalidReferences';
    }
}
