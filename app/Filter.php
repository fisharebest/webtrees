<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\CommonMark\CensusTableExtension;
use Fisharebest\Webtrees\CommonMark\XrefExtension;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Renderer\DocumentRenderer;
use League\CommonMark\Block\Renderer\ParagraphRenderer;
use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Parser\AutolinkParser;
use League\CommonMark\Inline\Renderer\LinkRenderer;
use League\CommonMark\Inline\Renderer\TextRenderer;
use Throwable;
use Webuni\CommonMark\TableExtension\TableExtension;

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
        $environment->mergeConfig([
            'renderer'           => [
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break'      => "\n",
            ],
            'html_input'         => Environment::HTML_INPUT_ESCAPE,
            'allow_unsafe_links' => true,
        ]);

        $environment
            ->addBlockRenderer(Document::class, new DocumentRenderer())
            ->addBlockRenderer(Paragraph::class, new ParagraphRenderer())
            ->addInlineRenderer(Text::class, new TextRenderer())
            ->addInlineRenderer(Link::class, new LinkRenderer())
            ->addInlineParser(new AutolinkParser());

        $environment->addExtension(new CensusTableExtension());
        $environment->addExtension(new XrefExtension($tree));

        $converter = new Converter(new DocParser($environment), new HtmlRenderer($environment));

        try {
            return $converter->convertToHtml($text);
        } catch (Throwable $ex) {
            // See issue #1824
            return $text;
        }
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
        $environment->mergeConfig(['html_input' => 'escape']);
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new CensusTableExtension());
        $environment->addExtension(new XrefExtension($tree));

        $converter = new Converter(new DocParser($environment), new HtmlRenderer($environment));

        try {
            return $converter->convertToHtml($text);
        } catch (Throwable $ex) {
            // See issue #1824
            return $text;
        }
    }
}
