<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

use phpDocumentor\Guides\RestructuredText\Builder\ParseQueue;
use PHPUnit\Framework\TestCase;

class ParseQueueTest extends TestCase
{
    public function testAddingFiles() : void
    {
        $parseQueue = new ParseQueue();
        $parseQueue->addFile('file_needs_parsing1', true);
        $parseQueue->addFile('file_no_parsing1', false);

        self::assertTrue($parseQueue->isFileKnownToParseQueue('file_needs_parsing1'));
        self::assertTrue($parseQueue->isFileKnownToParseQueue('file_no_parsing1'));
        self::assertFalse($parseQueue->isFileKnownToParseQueue('other_file'));

        self::assertTrue($parseQueue->doesFileRequireParsing('file_needs_parsing1'));
        self::assertFalse($parseQueue->doesFileRequireParsing('file_no_parsing1'));

        self::assertSame(['file_needs_parsing1'], $parseQueue->getAllFilesThatRequireParsing());
    }
}
