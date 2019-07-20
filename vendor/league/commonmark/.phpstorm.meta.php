<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPSTORM_META
{
    expectedArguments(\League\CommonMark\Context::setEncoding(), 0, 'UTF-8', 'ASCII', 'ISO-8859-1');
    expectedArguments(\League\CommonMark\Cursor::__construct(), 1, 'UTF-8', 'ASCII', 'ISO-8859-1');
    expectedArguments(\League\CommonMark\HtmlElement::__construct(), 0, 'a', 'abbr', 'address', 'area', 'article', 'aside', 'audio', 'b', 'base', 'bdi', 'bdo', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'cite', 'code', 'col', 'colgroup', 'data', 'datalist', 'dd', 'del', 'details', 'dfn', 'dialog', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup', 'hr', 'html', 'i', 'iframe', 'img', 'input', 'ins', 'kdb', 'keygen', 'label', 'legend', 'li', 'link', 'main', 'map', 'mark', 'menu', 'menuitem', 'meta', 'meter', 'nav', 'noscript', 'object', 'ol', 'optgroup', 'option', 'output', 'p', 'param', 'pre', 'progress', 'q', 's', 'samp', 'script', 'section', 'select', 'small', 'source', 'span', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'template', 'textarea', 'tfoot', 'th', 'thead', 'time', 'tr', 'track', 'u', 'ul', 'var', 'video', 'wbr');
    expectedArguments(\League\CommonMark\Block\Element\Heading::__construct(), 0, 1, 2, 3, 4, 5, 6);
    expectedReturnValues(\League\CommonMark\Block\Element\Heading::getLevel(), 1, 2, 3, 4, 5, 6);

    registerArgumentsSet('league_commonmark_htmlblock_types', \League\CommonMark\Block\Element\HtmlBlock::TYPE_1_CODE_CONTAINER, \League\CommonMark\Block\Element\HtmlBlock::TYPE_2_COMMENT, \League\CommonMark\Block\Element\HtmlBlock::TYPE_3, \League\CommonMark\Block\Element\HtmlBlock::TYPE_4, \League\CommonMark\Block\Element\HtmlBlock::TYPE_5_CDATA, \League\CommonMark\Block\Element\HtmlBlock::TYPE_6_BLOCK_ELEMENT, \League\CommonMark\Block\Element\HtmlBlock::TYPE_7_MISC_ELEMENT);
    expectedArguments(\League\CommonMark\Block\Element\HtmlBlock::__construct(), 0, argumentsSet('league_commonmark_htmlblock_types'));
    expectedArguments(\League\CommonMark\Block\Element\HtmlBlock::setType(), 0, argumentsSet('league_commonmark_htmlblock_types'));
    expectedReturnValues(\League\CommonMark\Block\Element\HtmlBlock::getType(), argumentsSet('league_commonmark_htmlblock_types'));
    expectedArguments(\League\CommonMark\Util\RegexHelper::getHtmlBlockOpenRegex(), 0, argumentsSet('league_commonmark_htmlblock_types'));
    expectedArguments(\League\CommonMark\Util\RegexHelper::getHtmlBlockCloseRegex(), 0, argumentsSet('league_commonmark_htmlblock_types'));

    registerArgumentsSet('league_commonmark_newline_types', \League\CommonMark\Inline\Element\Newline::HARDBREAK, \League\CommonMark\Inline\Element\Newline::SOFTBREAK);
    expectedArguments(\League\CommonMark\Inline\Element\Newline::__construct(), 0, argumentsSet('league_commonmark_newline_types'));
    expectedReturnValues(\League\CommonMark\Inline\Element\Newline::getType(), argumentsSet('league_commonmark_newline_types'));
}
