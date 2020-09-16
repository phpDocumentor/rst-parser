<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Meta;

use phpDocumentor\Guides\RestructuredText\Meta\CachedMetasLoader;
use phpDocumentor\Guides\RestructuredText\Meta\MetaEntry;
use phpDocumentor\Guides\RestructuredText\Meta\Metas;
use PHPUnit\Framework\TestCase;
use function sys_get_temp_dir;

class CachedMetasLoaderTest extends TestCase
{
    public function testSaveAndLoadCachedMetaEntries() : void
    {
        $targetDir = sys_get_temp_dir();
        $meta1     = new MetaEntry('file1', 'url1', 'title1', [], [], [], [], 0);
        $meta2     = new MetaEntry('file2', 'url2', 'title2', [], [], [], [], 0);

        $metasBefore = new Metas([$meta1, $meta2]);
        $metasAfter  = new Metas();

        $loader = new CachedMetasLoader();
        $loader->cacheMetaEntries($targetDir, $metasBefore);
        $loader->loadCachedMetaEntries($targetDir, $metasAfter);
        self::assertEquals([$meta1, $meta2], $metasAfter->getAll());
    }
}
