<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use Doctrine\Common\EventManager;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function str_repeat;

class LineCheckerTest extends TestCase
{
    /** @var Parser */
    private $parser;

    /** @var LineChecker */
    private $lineChecker;

    protected function setUp() : void
    {
        $this->parser = $this->createMock(Parser::class);

        /** @var EventManager|MockObject $eventManager */
        $eventManager = $this->createMock(EventManager::class);

        $this->lineChecker = new LineChecker(new LineDataParser($this->parser, $eventManager));
    }

    /**
     * @dataProvider getSpecialCharacters
     */
    public function testIsSpecialLine(string $specialCharacter) : void
    {
        self::assertNull($this->lineChecker->isSpecialLine($specialCharacter));
        self::assertSame($specialCharacter, $this->lineChecker->isSpecialLine(str_repeat($specialCharacter, 2)));
        self::assertSame($specialCharacter, $this->lineChecker->isSpecialLine(str_repeat($specialCharacter, 3)));
    }

    /**
     * @return string[][]
     */
    public function getSpecialCharacters() : array
    {
        return [['='], ['-'], ['~'], ['*'], ['+'], ['^'], ['"'], ['.'], ['`'], ["'"], ['_'], ['#'], [':']];
    }

    public function testIsListLine() : void
    {
        self::assertTrue($this->lineChecker->isListLine('- Test', true));
        self::assertTrue($this->lineChecker->isListLine('- Test', false));
        self::assertFalse($this->lineChecker->isListLine(' - Test', true));
        self::assertTrue($this->lineChecker->isListLine(' - Test', false));
    }

    public function testIsBlockLine() : void
    {
        self::assertTrue($this->lineChecker->isBlockLine(' '));
        self::assertTrue($this->lineChecker->isBlockLine('  '));
        self::assertTrue($this->lineChecker->isBlockLine('   '));
        self::assertFalse($this->lineChecker->isBlockLine('- Test'));
        self::assertFalse($this->lineChecker->isBlockLine('.. code-block::'));
    }

    public function testIsComment() : void
    {
        self::assertTrue($this->lineChecker->isComment('.. Test'));
        self::assertFalse($this->lineChecker->isComment('Test'));
    }

    public function testIsDirective() : void
    {
        self::assertTrue($this->lineChecker->isDirective('.. code-block::'));
        self::assertTrue($this->lineChecker->isDirective('.. code-block:: php'));
        self::assertTrue($this->lineChecker->isDirective('.. code&block:: php'));
        self::assertTrue($this->lineChecker->isDirective('.. `code-block`:: php'));
        self::assertFalse($this->lineChecker->isDirective('.. code block:: php'));
        self::assertFalse($this->lineChecker->isDirective('.. code block :: php'));
        self::assertFalse($this->lineChecker->isDirective('..code block:: php'));
        self::assertFalse($this->lineChecker->isDirective('.. code-block::php'));
        self::assertFalse($this->lineChecker->isDirective('Test'));
    }

    public function testIsDefinitionList() : void
    {
        self::assertTrue($this->lineChecker->isDefinitionList('    '));
        self::assertTrue($this->lineChecker->isDefinitionList('     '));
        self::assertFalse($this->lineChecker->isDefinitionList('Test'));
    }

    public function testIsDefinitionListEnded() : void
    {
        self::assertTrue($this->lineChecker->isDefinitionListEnded('Test', ''));
        self::assertFalse($this->lineChecker->isDefinitionListEnded('Term', '    Definition'));
        self::assertFalse($this->lineChecker->isDefinitionListEnded('', '    Definition'));
    }
}
