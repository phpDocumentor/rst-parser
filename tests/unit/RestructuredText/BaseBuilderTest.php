<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use phpDocumentor\Guides\RestructuredText\Builder;
use Exception;
use PHPUnit\Framework\TestCase;
use function file_get_contents;
use function shell_exec;

abstract class BaseBuilderTest extends TestCase
{
    /** @var Builder */
    protected $builder;

    abstract protected function getFixturesDirectory() : string;

    protected function setUp() : void
    {
        shell_exec('rm -rf ' . $this->targetFile());

        $kernel = new Kernel();
        $kernel->getConfiguration()->setUseCachedMetas(false);
        $this->builder = new Builder($kernel);
        $this->configureBuilder($kernel->getConfiguration());
        $this->builder->build($this->sourceFile(), $this->targetFile());
    }

    protected function configureBuilder(Configuration $configuration) : void
    {
    }

    protected function sourceFile(string $file = '') : string
    {
        return __DIR__ . '/' . $this->getFixturesDirectory() . '/input/' . $file;
    }

    protected function targetFile(string $file = '') : string
    {
        return __DIR__ . '/' . $this->getFixturesDirectory() . '/output/' . $file;
    }

    /**
     * @throws Exception
     */
    protected function getFileContents(string $path) : string
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new Exception('Could not load file.');
        }

        return $contents;
    }
}
