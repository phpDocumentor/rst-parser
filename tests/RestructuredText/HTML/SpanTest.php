<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML;

use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\Environment;
use phpDocumentor\Guides\RestructuredText\HTML\Renderers\SpanNodeRenderer;
use phpDocumentor\Guides\RestructuredText\Nodes\SpanNode;
use phpDocumentor\Guides\RestructuredText\Parser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SpanTest extends TestCase
{
    /**
     * @param string[] $attributes
     *
     * @dataProvider linkProvider
     */
    public function testLink(string $url, string $title, array $attributes, string $expectedLink) : void
    {
        /** @var Parser|MockObject $parser */
        $parser = $this->createMock(Parser::class);

        /** @var Environment|MockObject $environment */
        $environment = $this->createMock(Environment::class);

        $parser->expects(self::once())
            ->method('getEnvironment')
            ->willReturn($environment);

        $environment->expects(self::once())
            ->method('generateUrl')
            ->with($url)
            ->willReturn($url);

        $configuration    = new Configuration();
        $templateRenderer = $configuration->getTemplateRenderer();

        $span         = new SpanNode($parser, 'span');
        $spanRenderer = new SpanNodeRenderer($environment, $span, $templateRenderer);

        self::assertSame(
            $expectedLink,
            $spanRenderer->link($url, $title, $attributes)
        );
    }

    /**
     * @return string[][]|string[][][]
     */
    public function linkProvider() : array
    {
        return [
            'no attributes #1' => [
                'url'          => '#',
                'title'        => 'link',
                'attributes'   => [],
                'expectedLink' => '<a href="#">link</a>',
            ],

            'no attributes #2' => [
                'url'          => '/url?foo=bar&bar=foo',
                'title'        => 'link',
                'attributes'   => [],
                'expectedLink' => '<a href="/url?foo=bar&bar=foo">link</a>',
            ],

            'no attributes #3' => [
                'url'          => 'https://www.doctrine-project.org/',
                'title'        => 'link',
                'attributes'   => [],
                'expectedLink' => '<a href="https://www.doctrine-project.org/">link</a>',
            ],

            'with attributes #1' => [
                'url'          => '/url',
                'title'        => 'link',
                'attributes'   => ['class' => 'foo bar'],
                'expectedLink' => '<a href="/url" class="foo bar">link</a>',
            ],

            'with attributes #2' => [
                'url'          => '/url',
                'title'        => 'link',
                'attributes'   => ['class' => 'foo <>bar'],
                'expectedLink' => '<a href="/url" class="foo &lt;&gt;bar">link</a>',
            ],

            'with attributes #3' => [
                'url'          => '/url',
                'title'        => 'link',
                'attributes'   => ['class' => 'foo bar', 'data-id' => '123456'],
                'expectedLink' => '<a href="/url" class="foo bar" data-id="123456">link</a>',
            ],
        ];
    }
}
