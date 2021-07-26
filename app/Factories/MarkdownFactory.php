<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\CommonMark\ResponsiveTableExtension;
use Fisharebest\Webtrees\CommonMark\XrefExtension;
use Fisharebest\Webtrees\Contracts\MarkdownFactoryInterface;
use Fisharebest\Webtrees\Tree;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\LinkRenderer;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Renderer\Block\DocumentRenderer;
use League\CommonMark\Renderer\Block\ParagraphRenderer;
use League\CommonMark\Renderer\Inline\TextRenderer;
use League\CommonMark\Util\HtmlFilter;

/**
 * Create a markdown converter.
 */
class MarkdownFactory implements MarkdownFactoryInterface
{
    protected const CONFIG = [
        'allow_unsafe_links' => false,
        'html_input'         => HtmlFilter::ESCAPE,
    ];

    /**
     * @param Tree|null $tree
     *
     * @return MarkdownConverter
     */
    public function autolink(Tree $tree = null): MarkdownConverter
    {
        // Create a minimal commonmark processor - just add support for auto-links.
        $environment = new Environment(static::CONFIG);
        $environment->addRenderer(Document::class, new DocumentRenderer());
        $environment->addRenderer(Paragraph::class, new ParagraphRenderer());
        $environment->addRenderer(Text::class, new TextRenderer());
        $environment->addRenderer(Link::class, new LinkRenderer());
        $environment->addExtension(new AutolinkExtension());

        // Optionally create links to other records.
        if ($tree instanceof Tree) {
            $environment->addExtension(new XrefExtension($tree));
        }

        return new MarkdownConverter($environment);
    }

    /**
     * @param Tree|null $tree
     *
     * @return MarkdownConverter
     */
    public function markdown(Tree $tree = null): MarkdownConverter
    {
        $environment = $this->commonMarkEnvironment();

        // Wrap tables to support horizontal scrolling with bootstrap.
        $environment->addExtension(new ResponsiveTableExtension());

        // Convert webtrees 1.x style census tables to commonmark format.
        $environment->addExtension(new CensusTableExtension());

        // Optionally create links to other records.
        if ($tree instanceof Tree) {
            $environment->addExtension(new XrefExtension($tree));
        }

        return new MarkdownConverter($environment);
    }

    /**
     * @return Environment
     */
    protected function commonMarkEnvironment(): Environment
    {
        $environment = new Environment(static::CONFIG);
        $environment->addExtension(new CommonMarkCoreExtension());

        return $environment;
    }
}
