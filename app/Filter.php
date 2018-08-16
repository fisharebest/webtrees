<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\CommonMark\CensusTableExtension;
use Fisharebest\Webtrees\CommonMark\XrefExtension;
use League\CommonMark\Block\Renderer\DocumentRenderer;
use League\CommonMark\Block\Renderer\ParagraphRenderer;
use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
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
    const URL_REGEX = '((https?|ftp]):)(//([^\s/?#<>]*))?([^\s?#<>]*)(\?([^\s#<>]*))?(#[^\s?#<>]+)?';

    /**
     * Format block-level text such as notes or transcripts, etc.
     *
     * @param string $text
     * @param Tree   $tree
     *
     * @return string
     */
    public static function formatText($text, Tree $tree)
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
     * @param        Tree   tree
     *
     * @return string
     */
    public static function expandUrls($text, Tree $tree)
    {
        // If it looks like a URL, turn it into a markdown autolink.
        $text = preg_replace('/' . addcslashes(self::URL_REGEX, '/') . '/', '<$0>', $text);

        // Create a minimal commonmark processor - just add support for autolinks.
        $environment = new Environment;
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
            ->addBlockRenderer('League\CommonMark\Block\Element\Document', new DocumentRenderer)
            ->addBlockRenderer('League\CommonMark\Block\Element\Paragraph', new ParagraphRenderer)
            ->addInlineRenderer('League\CommonMark\Inline\Element\Text', new TextRenderer)
            ->addInlineRenderer('League\CommonMark\Inline\Element\Link', new LinkRenderer)
            ->addInlineParser(new AutolinkParser);

        $environment->addExtension(new CensusTableExtension);
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
    public static function markdown($text, Tree $tree)
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->mergeConfig(['html_input' => 'escape']);
        $environment->addExtension(new TableExtension);
        $environment->addExtension(new CensusTableExtension);
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
     * Validate INPUT parameters
     *
     * @param string      $source
     * @param string      $variable
     * @param string|null $regexp
     * @param string      $default
     *
     * @return string
     */
    private static function input($source, $variable, $regexp = null, $default = '')
    {
        if ($regexp) {
            return filter_input($source, $variable, FILTER_VALIDATE_REGEXP, [
                'options' => [
                    'regexp'  => '/^(' . $regexp . ')$/u',
                    'default' => $default,
                ],
            ]);
        } else {
            $tmp = filter_input($source, $variable, FILTER_CALLBACK, [
                'options' => function ($x) {
                    return mb_check_encoding($x, 'UTF-8') ? $x : false;
                },
            ]);

            return ($tmp === null || $tmp === false) ? $default : $tmp;
        }
    }

    /**
     * Validate array INPUT parameters
     *
     * @param string      $source
     * @param string      $variable
     * @param string|null $regexp
     * @param string      $default
     *
     * @return string[]
     */
    private static function inputArray($source, $variable, $regexp = null, $default = '')
    {
        if ($regexp) {
            return filter_input_array($source, [
                $variable => [
                    'flags'   => FILTER_REQUIRE_ARRAY,
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => [
                        'regexp'  => '/^(' . $regexp . ')$/u',
                        'default' => $default,
                    ],
                ],
            ])[$variable] ?: [];
        } else {
            return filter_input_array($source, [
                $variable => [
                    'flags'   => FILTER_REQUIRE_ARRAY,
                    'filter'  => FILTER_CALLBACK,
                    'options' => function ($x) {
                        return mb_check_encoding($x, 'UTF-8') ? $x : false;
                    },
                ],
            ])[$variable] ?: [];
        }
    }

    /**
     * Validate GET parameters
     *
     * @param string      $variable
     * @param string|null $regexp
     * @param string      $default
     *
     * @return string
     */
    public static function get($variable, $regexp = null, $default = '')
    {
        return self::input(INPUT_GET, $variable, $regexp, $default);
    }

    /**
     * Validate array GET parameters
     *
     * @param string      $variable
     * @param string|null $regexp
     * @param string      $default
     *
     * @return string[]
     */
    public static function getArray($variable, $regexp = null, $default = '')
    {
        return self::inputArray(INPUT_GET, $variable, $regexp, $default);
    }

    /**
     * Validate boolean GET parameters
     *
     * @param string $variable
     *
     * @return bool
     */
    public static function getBool($variable)
    {
        return (bool) filter_input(INPUT_GET, $variable, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Validate integer GET parameters
     *
     * @param string $variable
     * @param int    $min
     * @param int    $max
     * @param int    $default
     *
     * @return int
     */
    public static function getInteger($variable, $min = 0, $max = PHP_INT_MAX, $default = 0)
    {
        return filter_input(INPUT_GET, $variable, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => $min,
                'max_range' => $max,
                'default'   => $default,
            ],
        ]);
    }

    /**
     * Validate URL GET parameters
     *
     * @param string $variable
     * @param string $default
     *
     * @return string
     */
    public static function getUrl($variable, $default = '')
    {
        return filter_input(INPUT_GET, $variable, FILTER_VALIDATE_URL) ?: $default;
    }

    /**
     * Validate POST parameters
     *
     * @param string      $variable
     * @param string|null $regexp
     * @param string      $default
     *
     * @return string
     */
    public static function post($variable, $regexp = null, $default = '')
    {
        return self::input(INPUT_POST, $variable, $regexp, $default);
    }

    /**
     * Validate array POST parameters
     *
     * @param string      $variable
     * @param string|null $regexp
     * @param string      $default
     *
     * @return string[]|string[][]
     */
    public static function postArray($variable, $regexp = null, $default = '')
    {
        return self::inputArray(INPUT_POST, $variable, $regexp, $default);
    }

    /**
     * Validate boolean POST parameters
     *
     * @param string $variable
     *
     * @return bool
     */
    public static function postBool($variable)
    {
        return (bool) filter_input(INPUT_POST, $variable, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Validate integer POST parameters
     *
     * @param string $variable
     * @param int    $min
     * @param int    $max
     * @param int    $default
     *
     * @return int
     */
    public static function postInteger($variable, $min = 0, $max = PHP_INT_MAX, $default = 0)
    {
        return filter_input(INPUT_POST, $variable, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => $min,
                'max_range' => $max,
                'default'   => $default,
            ],
        ]);
    }

    /**
     * Validate URL GET parameters
     *
     * @param string $variable
     * @param string $default
     *
     * @return string
     */
    public static function postUrl($variable, $default = '')
    {
        return filter_input(INPUT_POST, $variable, FILTER_VALIDATE_URL) ?: $default;
    }

    /**
     * Validate COOKIE parameters
     *
     * @param string      $variable
     * @param string|null $regexp
     * @param string      $default
     *
     * @return string
     */
    public static function cookie($variable, $regexp = null, $default = '')
    {
        return self::input(INPUT_COOKIE, $variable, $regexp, $default);
    }

    /**
     * Validate SERVER parameters
     *
     * @param string      $variable
     * @param string|null $regexp
     * @param string      $default
     *
     * @return string
     */
    public static function server($variable, $regexp = null, $default = '')
    {
        // On some servers, variables that are present in $_SERVER cannot be
        // found via filter_input(INPUT_SERVER). Instead, they are found via
        // filter_input(INPUT_ENV). Since we cannot rely on filter_input(),
        // we must use the superglobal directly.
        if (array_key_exists($variable, $_SERVER) && ($regexp === null || preg_match('/^(' . $regexp . ')$/', $_SERVER[$variable]))) {
            return $_SERVER[$variable];
        } else {
            return $default;
        }
    }

    /**
     * Cross-Site Request Forgery tokens - ensure that the user is submitting
     * a form that was generated by the current session.
     *
     * @return string
     */
    public static function getCsrfToken()
    {
        if (!Session::has('CSRF_TOKEN')) {
            $charset    = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcedfghijklmnopqrstuvwxyz0123456789';
            $csrf_token = '';
            for ($n = 0; $n < 32; ++$n) {
                $csrf_token .= substr($charset, random_int(0, 61), 1);
            }
            Session::put('CSRF_TOKEN', $csrf_token);
        }

        return Session::get('CSRF_TOKEN');
    }

    /**
     * Generate an <input> element - to protect the current form from CSRF attacks.
     *
     * @return string
     */
    public static function getCsrf()
    {
        return '<input type="hidden" name="csrf" value="' . self::getCsrfToken() . '">';
    }

    /**
     * Check that the POST request contains the CSRF token generated above.
     *
     * @return bool
     */
    public static function checkCsrf()
    {
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN']) && $_SERVER['HTTP_X_CSRF_TOKEN'] !== self::getCsrfToken()) {
            // Oops. Something is not quite right
            Log::addAuthenticationLog('CSRF mismatch - session expired or malicious attack');
            FlashMessages::addMessage(I18N::translate('This form has expired. Try again.'), 'error');

            return false;
        }

        return true;
    }
}
