<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
namespace Fisharebest\Webtrees\Report;

/**
 * Class ReportParserBase
 */
class ReportParserBase {
	/** @var resource The XML parser */
	protected $xml_parser;

	/** @var string Text contents of tags */
	protected $text = '';

	/**
	 * Create a parser for a report
	 *
	 * @param string     $report     The XML filename
	 * @param ReportBase $report_root
	 * @param string[][] $vars
	 */
	public function __construct($report, ReportBase $report_root = null, $vars = array()) {
		$this->xml_parser = xml_parser_create();
		xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_element_handler($this->xml_parser, array($this, 'startElement'), array($this, 'endElement'));
		xml_set_character_data_handler($this->xml_parser, array($this, 'characterData'));

		$fp = fopen($report, 'r');
		while (($data = fread($fp, 4096))) {
			if (!xml_parse($this->xml_parser, $data, feof($fp))) {
				throw new \DomainException(sprintf(
					'XML error: %s at line %d',
					xml_error_string(xml_get_error_code($this->xml_parser)),
					xml_get_current_line_number($this->xml_parser)
				));
			}
		}

		xml_parser_free($this->xml_parser);
	}

	/**
	 * XML handler for an opening (or self-closing) tag.
	 *
	 * @param resource $parser The resource handler for the xml parser
	 * @param string   $name   The name of the xml element parsed
	 * @param string[] $attrs  An array of key value pairs for the attributes
	 */
	protected function startElement($parser, $name, $attrs) {
		$method = $name . 'StartHandler';
		if (method_exists($this, $method)) {
			$this->$method($attrs);
		}
	}

	/**
	 * XML handler for a closing tag.
	 *
	 * @param resource $parser the resource handler for the xml parser
	 * @param string $name the name of the xml element parsed
	 */
	protected function endElement($parser, $name) {
		$method = $name . 'EndHandler';
		if (method_exists($this, $method)) {
			$this->$method();
		}
	}

	/**
	 * XML handler for character data.
	 *
	 * @param resource $parser The resource handler for the xml parser
	 * @param string   $data   The name of the xml element parsed
	 */
	protected function characterData($parser, $data) {
		$this->text .= $data;
	}
}
