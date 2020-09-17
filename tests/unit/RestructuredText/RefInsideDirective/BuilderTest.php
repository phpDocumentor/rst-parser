<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\RefInsideDirective;

use phpDocumentor\Guides\RestructuredText\Builder;
use phpDocumentor\Guides\RestructuredText\Kernel;
use PHPUnit\Framework\TestCase;
use function file_get_contents;

class BuilderTest extends TestCase
{
    public function testRefInsideDirective() : void
    {
        $kernel  = new Kernel(null, [new VersionAddedDirective()]);
        $kernel->getConfiguration()->setUseCachedMetas(false);
        $builder = new Builder($kernel);

        $builder->build(
            __DIR__ . '/input',
            __DIR__ . '/output'
        );

        $expected = 'Test a reference in a directive <a href="file.html#some_reference">A file</a>.';

        self::assertContains($expected, file_get_contents(__DIR__ . '/output/index.html'));
    }
}
