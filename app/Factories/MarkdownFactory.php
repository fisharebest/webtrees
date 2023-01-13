<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\CommonMark\CensusTableExtension;
use Fisharebest\Webtrees\CommonMark\XrefExtension;
use Fisharebest\Webtrees\Contracts\MarkdownFactoryInterface;
use Fisharebest\Webtrees\Tree;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\NewlineParser;
use League\CommonMark\Renderer\Block\DocumentRenderer;
use League\CommonMark\Renderer\Block\ParagraphRenderer;
use League\CommonMark\Renderer\Inline\NewlineRenderer;
use League\CommonMark\Renderer\Inline\TextRenderer;
use League\CommonMark\Util\HtmlFilter;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\RegexHelper;

use function strip_tags;
use function strtr;

/**
 * Create a markdown converter.
 */
class MarkdownFactory implements MarkdownFactoryInterface
{
    // Commonmark uses the self-closing form of this tag, so we do the same for consistency.
    public const BREAK = '<br />';

    protected const CONFIG_AUTOLINK = [
        'allow_unsafe_links' => false,
        'html_input'         => HtmlFilter::ESCAPE,
        'renderer'           => [
            'soft_break' => self::BREAK,
        ],
        'external_link' => [
            'open_in_new_window' => true,
            'nofollow' => 'external',
            'noopener' => 'external',
            'noreferrer' => '',
        ],
    ];

    protected const CONFIG_MARKDOWN = [
        'allow_unsafe_links' => false,
        'html_input'         => HtmlFilter::ESCAPE,
        'renderer'           => [
            'soft_break' => self::BREAK,
        ],
        'table'              => [
            'wrap' => [
                'enabled'    => true,
                'tag'        => 'div',
                'attributes' => [
                    'class' => 'table-responsive',
                ],
            ],
        ],
    ];

    /**
     * @param string    $markdown
     * @param Tree|null $tree
     *
     * @return string
     */
    public function autolink(string $markdown, Tree $tree = null): string
    {
        // Create a minimal commonmark processor - just add support for auto-links.
        $environment = new Environment(static::CONFIG_AUTOLINK);
        $environment->addInlineParser(new NewlineParser());
        $environment->addRenderer(Document::class, new DocumentRenderer());
        $environment->addRenderer(Paragraph::class, new ParagraphRenderer());
        $environment->addRenderer(Text::class, new TextRenderer());
        $environment->addRenderer(Link::class, new ExternalLinkRenderer());
        $environment->addRenderer(Newline::class, new NewlineRenderer());
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new ExternalLinkExtension);

        // Optionally create links to other records.
        if ($tree instanceof Tree) {
            $environment->addExtension(new XrefExtension($tree));
        }

        $converter = new MarkDownConverter($environment);

        $html = $converter->convert($markdown)->getContent();

        // We should only have certain tags.  Make sure of this.
        $html = strip_tags($html, ['a', 'br', 'p']);

        // The markdown convert adds newlines, but not in a documented way.  Safest to ignore them.
        return strtr($html, ["\n"   => '']);
    }

    /**
     * @param string    $markdown
     * @param Tree|null $tree
     *
     * @return string
     */
    public function markdown(string $markdown, Tree $tree = null): string
    {
        $environment = new Environment(static::CONFIG_MARKDOWN);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());

        // Convert webtrees 1.x style census tables to commonmark format.
        $environment->addExtension(new CensusTableExtension());

        // Optionally create links to other records.
        if ($tree instanceof Tree) {
            $environment->addExtension(new XrefExtension($tree));
        }

        $converter = new MarkDownConverter($environment);

        $html = $converter->convert($markdown)->getContent();

        // The markdown convert adds newlines, but not in a documented way.  Safest to ignore them.
        return strtr($html, ["\n"   => '']);
    }
}

final class ExternalLinkRenderer implements NodeRendererInterface
{
    /**
     * @param Link $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        Link::assertInstanceOf($node);

        $attrs = $node->data->get('attributes');

        if (!(RegexHelper::isLinkPotentiallyUnsafe($node->getUrl()))) {
            $attrs['href'] = $node->getUrl();
        }

        if (($title = $node->getTitle()) !== null) {
            $attrs['title'] = $title;
        }

        if (isset($attrs['target']) && $attrs['target'] === '_blank' && !isset($attrs['rel'])) {
            $attrs['rel'] = 'noopener noreferrer';
        }

        if (preg_match('/\s?(http.?:\/\/.*\..*)(\/|\?).*/', $node->getUrl(), $matches) == 1) {
            if ($matches[2] == '/')
                $match  = $matches[1] . '/';
            else
                $match = $matches[1];
        } else
            $match =  $node->getUrl();

        return new HtmlElement('a', $attrs, $match);
    }
}
