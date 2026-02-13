<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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
use XMLParser;

use function method_exists;
use function sprintf;
use function xml_error_string;
use function xml_get_current_line_number;
use function xml_get_error_code;
use function xml_parse;
use function xml_parser_create;
use function xml_parser_set_option;
use function xml_set_character_data_handler;
use function xml_set_element_handler;

use const XML_OPTION_CASE_FOLDING;

class ReportParserBase
{
    protected XMLParser $xml_parser;

    protected string $text = '';

    public function __construct(string $report)
    {
        $this->xml_parser = xml_parser_create();
        xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, 0);
        xml_set_element_handler($this->xml_parser, $this->startElement(...), $this->endElement(...));
        xml_set_character_data_handler($this->xml_parser, $this->characterData(...));

        if (!xml_parse($this->xml_parser, file_get_contents($report), true)) {
            throw new DomainException(sprintf(
                'XML error: %s at line %d',
                xml_error_string(xml_get_error_code($this->xml_parser)),
                xml_get_current_line_number($this->xml_parser)
            ));
        }
    }

    protected function startElement(XMLParser $parser, string $name, array $attrs): void
    {
        $method = $name . 'StartHandler';

        if (method_exists($this, $method)) {
            $this->$method($attrs);
        }
    }

    protected function endElement(XMLParser $parser, string $name): void
    {
        $method = $name . 'EndHandler';

        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    protected function characterData(XMLParser $parser, string $data): void
    {
        $this->text .= $data;
    }
}
