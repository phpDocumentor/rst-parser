<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Functional;

use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\Formats\Format;
use phpDocumentor\Guides\RestructuredText\Kernel;
use phpDocumentor\Guides\RestructuredText\Parser;
use Exception;
use Gajus\Dindent\Indenter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use function array_map;
use function explode;
use function file_exists;
use function file_get_contents;
use function implode;
use function in_array;
use function rtrim;
use function sprintf;
use function str_replace;
use function strpos;
use function trim;

class FunctionalTest extends TestCase
{
    private const RENDER_DOCUMENT_FILES = ['main-directive'];

    /**
     * @dataProvider getFunctionalTests
     */
    public function testFunctional(
        string $file,
        Parser $parser,
        string $renderMethod,
        string $format,
        string $rst,
        string $expected
    ) : void {
        $expectedLines = explode("\n", $expected);
        $firstLine     = $expectedLines[0];

        if (strpos($firstLine, 'Exception:') === 0) {
            $exceptionClass = str_replace('Exception: ', '', $firstLine);
            $this->expectException($exceptionClass);

            $expectedExceptionMessage = $expectedLines;
            unset($expectedExceptionMessage[0]);
            $expectedExceptionMessage = implode("\n", $expectedExceptionMessage);

            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $document = $parser->parse($rst);

        $rendered = $document->$renderMethod();

        if ($format === Format::HTML) {
            $indenter = new Indenter();
            $rendered = $indenter->indent($rendered);
        }

        self::assertSame(
            $this->trimTrailingWhitespace($expected),
            $this->trimTrailingWhitespace($rendered)
        );
    }

    /**
     * @return mixed[]
     */
    public function getFunctionalTests() : array
    {
        $finder = new Finder();
        $finder
            ->directories()
            ->in(__DIR__ . '/tests');

        $tests = [];

        foreach ($finder as $dir) {
            $rstFilename = $dir->getPathname() . '/' . $dir->getFilename() . '.rst';
            if (! file_exists($rstFilename)) {
                throw new Exception(sprintf('Could not find functional test file "%s"', $rstFilename));
            }

            $rst      = file_get_contents($rstFilename);
            $basename = $dir->getFilename();

            $formats = [Format::HTML, Format::LATEX];

            $fileFinder = new Finder();
            $fileFinder
                ->files()
                ->in($dir->getPathname())
                ->notName('*.rst');
            foreach ($fileFinder as $file) {
                $format = $file->getExtension();
                if (! in_array($format, $formats, true)) {
                    throw new Exception(sprintf('Unexpected file extension in "%s"', $file->getPathname()));
                }

                if (strpos($file->getFilename(), $dir->getFilename()) !== 0) {
                    throw new Exception(sprintf('Test filename "%s" does not match directory name', $file->getPathname()));
                }

                $expected = $file->getContents();

                $configuration = new Configuration();
                $configuration->setFileExtension($format);

                $kernel = new Kernel($configuration);
                $parser = new Parser($kernel);

                $environment = $parser->getEnvironment();
                $environment->setCurrentDirectory(__DIR__ . '/tests/' . $basename);

                $renderMethod = in_array($basename, self::RENDER_DOCUMENT_FILES, true)
                    ? 'renderDocument'
                    : 'render';

                $tests[$basename . '_' . $format] = [$basename, $parser, $renderMethod, $format, $rst, trim($expected)];
            }
        }

        return $tests;
    }

    private function trimTrailingWhitespace(string $string) : string
    {
        $lines = explode("\n", $string);

        $lines = array_map(static function (string $line) {
            return rtrim($line);
        }, $lines);

        return trim(implode("\n", $lines));
    }
}
