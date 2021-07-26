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
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Renderer\DocumentRenderer;
use League\CommonMark\Block\Renderer\ParagraphRenderer;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Renderer\LinkRenderer;
use League\CommonMark\Inline\Renderer\TextRenderer;

/**
 * Create a markdown converter.
 */
class MarkdownFactory implements MarkdownFactoryInterface
{
    protected const CONFIG = [
        'allow_unsafe_links' => false,
        'html_input'         => Environment::HTML_INPUT_ESCAPE,
    ];

    /**
     * @param Tree|null $tree
     *
     * @return CommonMarkConverter
     */
    public function autolink(Tree $tree = null): CommonMarkConverter
    {
        // Create a minimal commonmark processor - just add support for auto-links.
        $environment = new Environment();
        $environment->addBlockRenderer(Document::class, new DocumentRenderer());
        $environment->addBlockRenderer(Paragraph::class, new ParagraphRenderer());
        $environment->addInlineRenderer(Text::class, new TextRenderer());
        $environment->addInlineRenderer(Link::class, new LinkRenderer());
        $environment->addExtension(new AutolinkExtension());

        // Optionally create links to other records.
        if ($tree instanceof Tree) {
            $environment->addExtension(new XrefExtension($tree));
        }

        return new CommonMarkConverter(static::CONFIG, $environment);
    }

    /**
     * @param Tree|null $tree
     *
     * @return CommonMarkConverter
     */
    public function markdown(Tree $tree = null): CommonMarkConverter
    {
        $environment = Environment::createCommonMarkEnvironment();

        // Wrap tables so support horizontal scrolling with bootstrap.
        $environment->addExtension(new ResponsiveTableExtension());

        // Convert webtrees 1.x style census tables to commonmark format.
        $environment->addExtension(new CensusTableExtension());

        // Optionally create links to other records.
        if ($tree instanceof Tree) {
            $environment->addExtension(new XrefExtension($tree));
        }

        return new CommonMarkConverter(static::CONFIG, $environment);
    }
}
