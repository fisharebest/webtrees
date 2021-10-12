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

namespace Fisharebest\Webtrees\Report;

use DomainException;
use Exception;
use Fisharebest\Webtrees\Registry;
use XMLParser;

use function call_user_func;
use function fclose;
use function feof;
use function fread;
use function method_exists;
use function sprintf;
use function xml_error_string;
use function xml_get_current_line_number;
use function xml_get_error_code;
use function xml_parse;
use function xml_parser_create;
use function xml_parser_free;
use function xml_parser_set_option;
use function xml_set_character_data_handler;
use function xml_set_element_handler;

use const XML_OPTION_CASE_FOLDING;

/**
 * Class ReportParserBase
 */
class ReportParserBase
{
    /** @var XMLParser (resource before PHP 8.0) The XML parser */
    protected $xml_parser;

    /** @var string Text contents of tags */
    protected string $text = '';

    /**
     * Create a parser for a report
     *
     * @param string $report The XML filename
     *
     * @throws Exception
     */
    public function __construct(string $report)
    {
        $this->xml_parser = xml_parser_create();

        xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, false);

        xml_set_element_handler(
            $this->xml_parser,
            function ($parser, string $name, array $attrs): void {
                $this->startElement($parser, $name, $attrs);
            },
            function ($parser, string $name): void {
                $this->endElement($parser, $name);
            }
        );

        xml_set_character_data_handler(
            $this->xml_parser,
            function ($parser, string $data): void {
                $this->characterData($parser, $data);
            }
        );

        $fp = Registry::filesystem()->root()->readStream($report);

        while ($data = fread($fp, 4096)) {
            if (!xml_parse($this->xml_parser, $data, feof($fp))) {
                throw new DomainException(sprintf(
                    'XML error: %s at line %d',
                    xml_error_string(xml_get_error_code($this->xml_parser)),
                    xml_get_current_line_number($this->xml_parser)
                ));
            }
        }

        fclose($fp);

        xml_parser_free($this->xml_parser);
    }

    /**
     * XML handler for an opening (or self-closing) tag.
     *
     * @param resource      $parser The resource handler for the xml parser
     * @param string        $name   The name of the xml element parsed
     * @param array<string> $attrs  An array of key value pairs for the attributes
     *
     * @return void
     */
    protected function startElement($parser, string $name, array $attrs): void
    {
        $method = $name . 'StartHandler';

        if (method_exists($this, $method)) {
            call_user_func([$this, $method], $attrs);
        }
    }

    /**
     * XML handler for a closing tag.
     *
     * @param resource $parser the resource handler for the xml parser
     * @param string   $name   the name of the xml element parsed
     *
     * @return void
     */
    protected function endElement($parser, string $name): void
    {
        $method = $name . 'EndHandler';

        if (method_exists($this, $method)) {
            call_user_func([$this, $method]);
        }
    }

    /**
     * XML handler for character data.
     *
     * @param resource $parser The resource handler for the xml parser
     * @param string   $data   The name of the xml element parsed
     *
     * @return void
     */
    protected function characterData($parser, string $data): void
    {
        $this->text .= $data;
    }
}
