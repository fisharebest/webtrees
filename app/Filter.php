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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\CommonMark\CensusTableExtension;
use Fisharebest\Webtrees\CommonMark\ResponsiveTableExtension;
use Fisharebest\Webtrees\CommonMark\XrefExtension;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Renderer\DocumentRenderer;
use League\CommonMark\Block\Renderer\ParagraphRenderer;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Parser\AutolinkParser;
use League\CommonMark\Inline\Renderer\LinkRenderer;
use League\CommonMark\Inline\Renderer\TextRenderer;

/**
 * Filter input and escape output.
 */
class Filter
{
    // REGEX to match a URL
    // Some versions of RFC3987 have an appendix B which gives the following regex
    // (([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?
    // This matches far too much while a “precise” regex is several pages long.
    // This is a compromise.
    private const URL_REGEX = '((https?|ftp]):)(//([^\s/?#<>]*))?([^\s?#<>]*)(\?([^\s#<>]*))?(#[^\s?#<>]+)?';

    /**
     * Format block-level text such as notes or transcripts, etc.
     *
     * @param string $text
     * @param Tree   $tree
     *
     * @return string
     */
    public static function formatText(string $text, Tree $tree): string
    {
        switch ($tree->getPreference('FORMAT_TEXT')) {
            case 'markdown':
                return '<div class="markdown" dir="auto">' . self::markdown($text, $tree) . '</div>';
            default:
                return '<div class="markdown" style="white-space: pre-wrap;" dir="auto">' . self::expandUrls($text, $tree) . '</div>';
        }
    }

    /**
     * Format a block of text, expanding URLs and XREFs.
     *
     * @param string $text
     * @param Tree   $tree
     *
     * @return string
     */
    public static function expandUrls(string $text, Tree $tree): string
    {
        // If it looks like a URL, turn it into a markdown autolink.
        $text = preg_replace('/' . addcslashes(self::URL_REGEX, '/') . '/', '<$0>', $text);

        // Create a minimal commonmark processor - just add support for autolinks.
        $environment = new Environment();
        $environment
            ->addBlockRenderer(Document::class, new DocumentRenderer())
            ->addBlockRenderer(Paragraph::class, new ParagraphRenderer())
            ->addInlineRenderer(Text::class, new TextRenderer())
            ->addInlineRenderer(Link::class, new LinkRenderer())
            ->addInlineParser(new AutolinkParser())
            ->addExtension(new XrefExtension($tree));

        $converter = new CommonMarkConverter(['html_input' => Environment::HTML_INPUT_ESCAPE], $environment);

        return $converter->convertToHtml($text);
    }

    /**
     * Format a block of text, using "Markdown".
     *
     * @param string $text
     * @param Tree   $tree
     *
     * @return string
     */
    public static function markdown(string $text, Tree $tree): string
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new ResponsiveTableExtension());
        $environment->addExtension(new CensusTableExtension());
        $environment->addExtension(new XrefExtension($tree));

        $config = [
            'allow_unsafe_links' => false,
            'html_input'         => Environment::HTML_INPUT_ESCAPE,
        ];

        $converter = new CommonMarkConverter($config, $environment);

        return $converter->convertToHtml($text);
    }
}
