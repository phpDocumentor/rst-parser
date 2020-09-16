<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LiteralNestedInDirective;

use phpDocumentor\Guides\RestructuredText\Builder;
use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\Kernel;
use phpDocumentor\Guides\RestructuredText\BaseBuilderTest;
use function shell_exec;

/**
 * Unit testing for RST
 */
class BuilderTest extends BaseBuilderTest
{
    protected function setUp() : void
    {
        shell_exec('rm -rf ' . $this->targetFile());

        $kernel = new Kernel(
            new Configuration(),
            [new TipDirective()]
        );

        $this->builder = new Builder($kernel);
        $this->builder->getConfiguration()->setUseCachedMetas(false);

        $this->builder->build($this->sourceFile(), $this->targetFile());
    }

    public function testLiteralNestedInDirective() : void
    {
        $contents = $this->getFileContents($this->targetFile('index.html'));

        self::assertContains('class="tip"', $contents);
        self::assertContains('<code', $contents);
        self::assertContains('</code>', $contents);
    }

    protected function getFixturesDirectory() : string
    {
        return 'LiteralNestedInDirective';
    }
}
