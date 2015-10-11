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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Place;

/**
 * Class ReportParserGenerate - parse a report.xml file and generate the report.
 */
class ReportParserGenerate extends ReportParserBase {
	/** @var bool Are we collecting data from <Footnote> elements  */
	private $process_footnote = true;

	/** @var bool Are we currently outputing data? */
	private $print_data = false;

	/** @var bool[] Push-down stack of $print_data */
	private $print_data_stack = array();

	/** @var int Are we processing GEDCOM data */
	private $process_gedcoms = 0;

	/** @var int Are we processing conditionals */
	private $process_ifs = 0;

	/** @var int Are we processing repeats*/
	private $process_repeats = 0;

	/** @var int Quantity of data to repeat during loops */
	private $repeat_bytes = 0;

	/** @var array[] Repeated data when iterating over loops */
	private $repeats = array();

	/** @var array[] Nested repeating data */
	private $repeats_stack = array();

	/** @var ReportBase[] Nested repeating data */
	private $wt_report_stack = array();

	/** @var resource Nested repeating data */
	private $parser;

	/** @var resource[] Nested repeating data */
	private $parser_stack = array();

	/** @var string The current GEDCOM record */
	private $gedrec = '';

	/** @var string[] Nested GEDCOM records */
	private $gedrec_stack = array();

	/** @var ReportBaseElement The currently processed element */
	private $current_element;

	/** @var ReportBaseElement The currently processed element */
	private $footnote_element;

	/** @var string The GEDCOM fact currently being processed */
	private $fact = '';

	/** @var string The GEDCOM value currently being processed */
	private $desc = '';

	/** @var string The GEDCOM type currently being processed */
	private $type = '';

	/** @var int The current generational level */
	private $generation = 1;

	/** @var array Source data for processing lists */
	private $list = array();

	/** @var int Number of items in lists */
	private $list_total = 0;

	/** @var int Number of items filtered from lists */
	private $list_private = 0;

	/** @var ReportBase A factory for creating report elements */
	private $report_root;

	/** @var ReportBase Nested report elements */
	private $wt_report;

	/** @todo This attribute is public to support the PHP5.3 closure workaround. */
	/** @var string[][] Variables defined in the report at run-time */
	public $vars;

	/**
	 * Create a parser for a report
	 *
	 * @param string     $report     The XML filename
	 * @param ReportBase $report_root
	 * @param string[][] $vars
	 */
	public function __construct($report, ReportBase $report_root = null, array $vars = array()) {
		$this->report_root     = $report_root;
		$this->wt_report       = $report_root;
		$this->current_element = new ReportBaseElement;
		$this->vars            = $vars;
		parent::__construct($report);
	}

	/**
	 * XML start element handler
	 *
	 * This function is called whenever a starting element is reached
	 * The element handler will be called if found, otherwise it must be HTML
	 *
	 * @param resource $parser the resource handler for the XML parser
	 * @param string   $name   the name of the XML element parsed
	 * @param array    $attrs  an array of key value pairs for the attributes
	 */
	protected function startElement($parser, $name, $attrs) {
		$newattrs = array();

		foreach ($attrs as $key => $value) {
			if (preg_match("/^\\$(\w+)$/", $value, $match)) {
				if ((isset($this->vars[$match[1]]['id'])) && (!isset($this->vars[$match[1]]['gedcom']))) {
					$value = $this->vars[$match[1]]['id'];
				}
			}
			$newattrs[$key] = $value;
		}
		$attrs = $newattrs;
		if ($this->process_footnote && ($this->process_ifs === 0 || $name === "if") && ($this->process_gedcoms === 0 || $name === "Gedcom") && ($this->process_repeats === 0 || $name === "Facts" || $name === "RepeatTag")) {
			$start_method = $name . 'StartHandler';
			$end_method   = $name . 'EndHandler';
			if (method_exists($this, $start_method)) {
				$this->$start_method($attrs);
			} elseif (!method_exists($this, $end_method)) {
				$this->htmlStartHandler($name, $attrs);
			}
		}
	}

	/**
	 * XML end element handler
	 *
	 * This function is called whenever an ending element is reached
	 * The element handler will be called if found, otherwise it must be HTML
	 *
	 * @param resource $parser the resource handler for the XML parser
	 * @param string   $name   the name of the XML element parsed
	 */
	protected function endElement($parser, $name) {
		if (($this->process_footnote || $name === "Footnote") && ($this->process_ifs === 0 || $name === "if") && ($this->process_gedcoms === 0 || $name === "Gedcom") && ($this->process_repeats === 0 || $name === "Facts" || $name === "RepeatTag" || $name === "List" || $name === "Relatives")) {
			$start_method = $name . 'StartHandler';
			$end_method   = $name . 'EndHandler';
			if (method_exists($this, $end_method)) {
				$this->$end_method();
			} elseif (!method_exists($this, $start_method)) {
				$this->htmlEndHandler($name);
			}
		}
	}

	/**
	 * XML character data handler
	 *
	 * @param resource $parser the resource handler for the XML parser
	 * @param string   $data   the name of the XML element parsed
	 */
	protected function characterData($parser, $data) {
		if ($this->print_data && $this->process_gedcoms === 0 && $this->process_ifs === 0 && $this->process_repeats === 0) {
			$this->current_element->addText($data);
		}
	}

	/**
	 * XML <style>
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function styleStartHandler($attrs) {
		if (empty($attrs['name'])) {
			throw new \DomainException('REPORT ERROR Style: The "name" of the style is missing or not set in the XML file.');
		}

		// array Style that will be passed on
		$s = array();

		// string Name af the style
		$s['name'] = $attrs['name'];

		// string Name of the DEFAULT font
		$s['font'] = $this->wt_report->defaultFont;
		if (!empty($attrs['font'])) {
			$s['font'] = $attrs['font'];
		}

		// int The size of the font in points
		$s['size'] = $this->wt_report->defaultFontSize;
		if (!empty($attrs['size'])) {
			$s['size'] = (int) $attrs['size'];
		} // Get it as int to ignore all decimal points or text (if any text then int(0))

		// string B: bold, I: italic, U: underline, D: line trough, The default value is regular.
		$s['style'] = "";
		if (!empty($attrs['style'])) {
			$s['style'] = $attrs['style'];
		}

		$this->wt_report->addStyle($s);
	}

	/**
	 * XML <Doc>
	 *
	 * Sets up the basics of the document proparties
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function docStartHandler($attrs) {
		$this->parser = $this->xml_parser;

		// Custom page width
		if (!empty($attrs['customwidth'])) {
			$this->wt_report->pagew = (int) $attrs['customwidth'];
		} // Get it as int to ignore all decimal points or text (if any text then int(0))
		// Custom Page height
		if (!empty($attrs['customheight'])) {
			$this->wt_report->pageh = (int) $attrs['customheight'];
		} // Get it as int to ignore all decimal points or text (if any text then int(0))

		// Left Margin
		if (isset($attrs['leftmargin'])) {
			if ($attrs['leftmargin'] === "0") {
				$this->wt_report->leftmargin = 0;
			} elseif (!empty($attrs['leftmargin'])) {
				$this->wt_report->leftmargin = (int) $attrs['leftmargin']; // Get it as int to ignore all decimal points or text (if any text then int(0))
			}
		}
		// Right Margin
		if (isset($attrs['rightmargin'])) {
			if ($attrs['rightmargin'] === "0") {
				$this->wt_report->rightmargin = 0;
			} elseif (!empty($attrs['rightmargin'])) {
				$this->wt_report->rightmargin = (int) $attrs['rightmargin']; // Get it as int to ignore all decimal points or text (if any text then int(0))
			}
		}
		// Top Margin
		if (isset($attrs['topmargin'])) {
			if ($attrs['topmargin'] === "0") {
				$this->wt_report->topmargin = 0;
			} elseif (!empty($attrs['topmargin'])) {
				$this->wt_report->topmargin = (int) $attrs['topmargin']; // Get it as int to ignore all decimal points or text (if any text then int(0))
			}
		}
		// Bottom Margin
		if (isset($attrs['bottommargin'])) {
			if ($attrs['bottommargin'] === "0") {
				$this->wt_report->bottommargin = 0;
			} elseif (!empty($attrs['bottommargin'])) {
				$this->wt_report->bottommargin = (int) $attrs['bottommargin']; // Get it as int to ignore all decimal points or text (if any text then int(0))
			}
		}
		// Header Margin
		if (isset($attrs['headermargin'])) {
			if ($attrs['headermargin'] === "0") {
				$this->wt_report->headermargin = 0;
			} elseif (!empty($attrs['headermargin'])) {
				$this->wt_report->headermargin = (int) $attrs['headermargin']; // Get it as int to ignore all decimal points or text (if any text then int(0))
			}
		}
		// Footer Margin
		if (isset($attrs['footermargin'])) {
			if ($attrs['footermargin'] === "0") {
				$this->wt_report->footermargin = 0;
			} elseif (!empty($attrs['footermargin'])) {
				$this->wt_report->footermargin = (int) $attrs['footermargin']; // Get it as int to ignore all decimal points or text (if any text then int(0))
			}
		}

		// Page Orientation
		if (!empty($attrs['orientation'])) {
			if ($attrs['orientation'] == "landscape") {
				$this->wt_report->orientation = "landscape";
			} elseif ($attrs['orientation'] == "portrait") {
				$this->wt_report->orientation = "portrait";
			}
		}
		// Page Size
		if (!empty($attrs['pageSize'])) {
			$this->wt_report->pageFormat = strtoupper($attrs['pageSize']);
		}

		// Show Generated By...
		if (isset($attrs['showGeneratedBy'])) {
			if ($attrs['showGeneratedBy'] === "0") {
				$this->wt_report->showGenText = false;
			} elseif ($attrs['showGeneratedBy'] === "1") {
				$this->wt_report->showGenText = true;
			}
		}

		$this->wt_report->setup();
	}

	/**
	 * XML </Doc>
	 */
	private function docEndHandler() {
		$this->wt_report->run();
	}

	/**
	 * XML <Header>
	 */
	private function headerStartHandler() {
		// Clear the Header before any new elements are added
		$this->wt_report->clearHeader();
		$this->wt_report->setProcessing("H");
	}

	/**
	 * XML <PageHeader>
	 */
	private function pageHeaderStartHandler() {
		array_push($this->print_data_stack, $this->print_data);
		$this->print_data = false;
		array_push($this->wt_report_stack, $this->wt_report);
		$this->wt_report = $this->report_root->createPageHeader();
	}

	/**
	 * XML <pageHeaderEndHandler>
	 */
	private function pageHeaderEndHandler() {
		$this->print_data        = array_pop($this->print_data_stack);
		$this->current_element   = $this->wt_report;
		$this->wt_report         = array_pop($this->wt_report_stack);
		$this->wt_report->addElement($this->current_element);
	}

	/**
	 * XML <bodyStartHandler>
	 */
	private function bodyStartHandler() {
		$this->wt_report->setProcessing("B");
	}

	/**
	 * XML <footerStartHandler>
	 */
	private function footerStartHandler() {
		$this->wt_report->setProcessing("F");
	}

	/**
	 * XML <Cell>
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function cellStartHandler($attrs) {
		// string The text alignment of the text in this box.
		$align = "";
		if (!empty($attrs['align'])) {
			$align = $attrs['align'];
			// RTL supported left/right alignment
			if ($align == "rightrtl") {
				if ($this->wt_report->rtl) {
					$align = "left";
				} else {
					$align = "right";
				}
			} elseif ($align == "leftrtl") {
				if ($this->wt_report->rtl) {
					$align = "right";
				} else {
					$align = "left";
				}
			}
		}

		// string The color to fill the background of this cell
		$bgcolor = "";
		if (!empty($attrs['bgcolor'])) {
			$bgcolor = $attrs['bgcolor'];
		}

		// int Whether or not the background should be painted
		$fill = 1;
		if (isset($attrs['fill'])) {
			if ($attrs['fill'] === "0") {
				$fill = 0;
			} elseif ($attrs['fill'] === "1") {
				$fill = 1;
			}
		}

		$reseth = true;
		// boolean   if true reset the last cell height (default true)
		if (isset($attrs['reseth'])) {
			if ($attrs['reseth'] === "0") {
				$reseth = false;
			} elseif ($attrs['reseth'] === "1") {
				$reseth = true;
			}
		}

		// mixed Whether or not a border should be printed around this box
		$border = 0;
		if (!empty($attrs['border'])) {
			$border = $attrs['border'];
		}
		// string Border color in HTML code
		$bocolor = "";
		if (!empty($attrs['bocolor'])) {
			$bocolor = $attrs['bocolor'];
		}

		// int Cell height (expressed in points) The starting height of this cell. If the text wraps the height will automatically be adjusted.
		$height = 0;
		if (!empty($attrs['height'])) {
			$height = (int) $attrs['height'];
		}
		// int Cell width (expressed in points) Setting the width to 0 will make it the width from the current location to the right margin.
		$width = 0;
		if (!empty($attrs['width'])) {
			$width = (int) $attrs['width'];
		}

		// int Stretch carachter mode
		$stretch = 0;
		if (!empty($attrs['stretch'])) {
			$stretch = (int) $attrs['stretch'];
		}

		// mixed Position the left corner of this box on the page. The default is the current position.
		$left = ".";
		if (isset($attrs['left'])) {
			if ($attrs['left'] === ".") {
				$left = ".";
			} elseif (!empty($attrs['left'])) {
				$left = (int) $attrs['left'];
			} elseif ($attrs['left'] === "0") {
				$left = 0;
			}
		}
		// mixed Position the top corner of this box on the page. the default is the current position
		$top = ".";
		if (isset($attrs['top'])) {
			if ($attrs['top'] === ".") {
				$top = ".";
			} elseif (!empty($attrs['top'])) {
				$top = (int) $attrs['top'];
			} elseif ($attrs['top'] === "0") {
				$top = 0;
			}
		}

		// string The name of the Style that should be used to render the text.
		$style = "";
		if (!empty($attrs['style'])) {
			$style = $attrs['style'];
		}

		// string Text color in html code
		$tcolor = "";
		if (!empty($attrs['tcolor'])) {
			$tcolor = $attrs['tcolor'];
		}

		// int Indicates where the current position should go after the call.
		$ln = 0;
		if (isset($attrs['newline'])) {
			if (!empty($attrs['newline'])) {
				$ln = (int) $attrs['newline'];
			} elseif ($attrs['newline'] === "0") {
				$ln = 0;
			}
		}

		if ($align == "left") {
			$align = "L";
		} elseif ($align == "right") {
			$align = "R";
		} elseif ($align == "center") {
			$align = "C";
		} elseif ($align == "justify") {
			$align = "J";
		}

		array_push($this->print_data_stack, $this->print_data);
		$this->print_data = true;

		$this->current_element = $this->report_root->createCell(
			$width,
			$height,
			$border,
			$align,
			$bgcolor,
			$style,
			$ln,
			$top,
			$left,
			$fill,
			$stretch,
			$bocolor,
			$tcolor,
			$reseth
		);
	}

	/**
	 * XML </Cell>
	 */
	private function cellEndHandler() {
		$this->print_data = array_pop($this->print_data_stack);
		$this->wt_report->addElement($this->current_element);
	}

	/**
	 * XML <Now /> element handler
	 */
	private function nowStartHandler() {
		$g = FunctionsDate::timestampToGedcomDate(WT_TIMESTAMP + WT_TIMESTAMP_OFFSET);
		$this->current_element->addText($g->display());
	}

	/**
	 * XML <PageNum /> element handler
	 */
	private function pageNumStartHandler() {
		$this->current_element->addText("#PAGENUM#");
	}

	/**
	 * XML <TotalPages /> element handler
	 */
	private function totalPagesStartHandler() {
		$this->current_element->addText("{{:ptp:}}");
	}

	/**
	 * Called at the start of an element.
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function gedcomStartHandler($attrs) {
		global $WT_TREE;

		if ($this->process_gedcoms > 0) {
			$this->process_gedcoms++;

			return;
		}

		$tag       = $attrs['id'];
		$tag       = str_replace("@fact", $this->fact, $tag);
		$tags      = explode(":", $tag);
		$newgedrec = '';
		if (count($tags) < 2) {
			$tmp       = GedcomRecord::getInstance($attrs['id'], $WT_TREE);
			$newgedrec = $tmp ? $tmp->privatizeGedcom(Auth::accessLevel($WT_TREE)) : '';
		}
		if (empty($newgedrec)) {
			$tgedrec   = $this->gedrec;
			$newgedrec = '';
			foreach ($tags as $tag) {
				if (preg_match("/\\$(.+)/", $tag, $match)) {
					if (isset($this->vars[$match[1]]['gedcom'])) {
						$newgedrec = $this->vars[$match[1]]['gedcom'];
					} else {
						$tmp       = GedcomRecord::getInstance($match[1], $WT_TREE);
						$newgedrec = $tmp ? $tmp->privatizeGedcom(Auth::accessLevel($WT_TREE)) : '';
					}
				} else {
					if (preg_match("/@(.+)/", $tag, $match)) {
						$gmatch = array();
						if (preg_match("/\d $match[1] @([^@]+)@/", $tgedrec, $gmatch)) {
							$tmp       = GedcomRecord::getInstance($gmatch[1], $WT_TREE);
							$newgedrec = $tmp ? $tmp->privatizeGedcom(Auth::accessLevel($WT_TREE)) : '';
							$tgedrec   = $newgedrec;
						} else {
							$newgedrec = '';
							break;
						}
					} else {
						$temp      = explode(" ", trim($tgedrec));
						$level     = $temp[0] + 1;
						$newgedrec = Functions::getSubRecord($level, "$level $tag", $tgedrec);
						$tgedrec   = $newgedrec;
					}
				}
			}
		}
		if (!empty($newgedrec)) {
			array_push($this->gedrec_stack, array($this->gedrec, $this->fact, $this->desc));
			$this->gedrec = $newgedrec;
			if (preg_match("/(\d+) (_?[A-Z0-9]+) (.*)/", $this->gedrec, $match)) {
				$this->fact = $match[2];
				$this->desc = trim($match[3]);
			}
		} else {
			$this->process_gedcoms++;
		}
	}

	/**
	 * Called at the end of an element.
	 */
	private function gedcomEndHandler() {
		if ($this->process_gedcoms > 0) {
			$this->process_gedcoms--;
		} else {
			list($this->gedrec, $this->fact, $this->desc) = array_pop($this->gedrec_stack);
		}
	}

	/**
	 * XML <textBoxStartHandler>
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function textBoxStartHandler($attrs) {
		// string Background color code
		$bgcolor = "";
		if (!empty($attrs['bgcolor'])) {
			$bgcolor = $attrs['bgcolor'];
		}

		// boolean Wether or not fill the background color
		$fill = true;
		if (isset($attrs['fill'])) {
			if ($attrs['fill'] === "0") {
				$fill = false;
			} elseif ($attrs['fill'] === "1") {
				$fill = true;
			}
		}

		// var boolean Whether or not a border should be printed around this box. 0 = no border, 1 = border. Default is 0
		$border = false;
		if (isset($attrs['border'])) {
			if ($attrs['border'] === "1") {
				$border = true;
			} elseif ($attrs['border'] === "0") {
				$border = false;
			}
		}

		// int The starting height of this cell. If the text wraps the height will automatically be adjusted
		$height = 0;
		if (!empty($attrs['height'])) {
			$height = (int) $attrs['height'];
		}
		// int Setting the width to 0 will make it the width from the current location to the margin
		$width = 0;
		if (!empty($attrs['width'])) {
			$width = (int) $attrs['width'];
		}

		// mixed Position the left corner of this box on the page. The default is the current position.
		$left = ".";
		if (isset($attrs['left'])) {
			if ($attrs['left'] === ".") {
				$left = ".";
			} elseif (!empty($attrs['left'])) {
				$left = (int) $attrs['left'];
			} elseif ($attrs['left'] === "0") {
				$left = 0;
			}
		}
		// mixed Position the top corner of this box on the page. the default is the current position
		$top = ".";
		if (isset($attrs['top'])) {
			if ($attrs['top'] === ".") {
				$top = ".";
			} elseif (!empty($attrs['top'])) {
				$top = (int) $attrs['top'];
			} elseif ($attrs['top'] === "0") {
				$top = 0;
			}
		}
		// boolean After this box is finished rendering, should the next section of text start immediately after the this box or should it start on a new line under this box. 0 = no new line, 1 = force new line. Default is 0
		$newline = false;
		if (isset($attrs['newline'])) {
			if ($attrs['newline'] === "1") {
				$newline = true;
			} elseif ($attrs['newline'] === "0") {
				$newline = false;
			}
		}
		// boolean
		$pagecheck = true;
		if (isset($attrs['pagecheck'])) {
			if ($attrs['pagecheck'] === "0") {
				$pagecheck = false;
			} elseif ($attrs['pagecheck'] === "1") {
				$pagecheck = true;
			}
		}
		// boolean Cell padding
		$padding = true;
		if (isset($attrs['padding'])) {
			if ($attrs['padding'] === "0") {
				$padding = false;
			} elseif ($attrs['padding'] === "1") {
				$padding = true;
			}
		}
		// boolean Reset this box Height
		$reseth = false;
		if (isset($attrs['reseth'])) {
			if ($attrs['reseth'] === "1") {
				$reseth = true;
			} elseif ($attrs['reseth'] === "0") {
				$reseth = false;
			}
		}

		// string Style of rendering
		$style = "";

		array_push($this->print_data_stack, $this->print_data);
		$this->print_data = false;

		array_push($this->wt_report_stack, $this->wt_report);
		$this->wt_report = $this->report_root->createTextBox(
			$width,
			$height,
			$border,
			$bgcolor,
			$newline,
			$left,
			$top,
			$pagecheck,
			$style,
			$fill,
			$padding,
			$reseth
		);
	}

	/**
	 * XML <textBoxEndHandler>
	 */
	private function textBoxEndHandler() {
		$this->print_data      = array_pop($this->print_data_stack);
		$this->current_element = $this->wt_report;
		$this->wt_report       = array_pop($this->wt_report_stack);
		$this->wt_report->addElement($this->current_element);
	}

	/**
	 * XLM <Text>.
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function textStartHandler($attrs) {
		array_push($this->print_data_stack, $this->print_data);
		$this->print_data = true;

		// string The name of the Style that should be used to render the text.
		$style = "";
		if (!empty($attrs['style'])) {
			$style = $attrs['style'];
		}

		// string  The color of the text - Keep the black color as default
		$color = "";
		if (!empty($attrs['color'])) {
			$color = $attrs['color'];
		}

		$this->current_element = $this->report_root->createText($style, $color);
	}

	/**
	 * XML </Text>
	 */
	private function textEndHandler() {
		$this->print_data = array_pop($this->print_data_stack);
		$this->wt_report->addElement($this->current_element);
	}

	/**
	 * XML <GetPersonName/>
	 *
	 * Get the name
	 * 1. id is empty - current GEDCOM record
	 * 2. id is set with a record id
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function getPersonNameStartHandler($attrs) {
		global $WT_TREE;

		$id    = "";
		$match = array();
		if (empty($attrs['id'])) {
			if (preg_match("/0 @(.+)@/", $this->gedrec, $match)) {
				$id = $match[1];
			}
		} else {
			if (preg_match("/\\$(.+)/", $attrs['id'], $match)) {
				if (isset($this->vars[$match[1]]['id'])) {
					$id = $this->vars[$match[1]]['id'];
				}
			} else {
				if (preg_match("/@(.+)/", $attrs['id'], $match)) {
					$gmatch = array();
					if (preg_match("/\d $match[1] @([^@]+)@/", $this->gedrec, $gmatch)) {
						$id = $gmatch[1];
					}
				} else {
					$id = $attrs['id'];
				}
			}
		}
		if (!empty($id)) {
			$record = GedcomRecord::getInstance($id, $WT_TREE);
			if (is_null($record)) {
				return;
			}
			if (!$record->canShowName()) {
				$this->current_element->addText(I18N::translate('Private'));
			} else {
				$name = $record->getFullName();
				$name = preg_replace(
					array('/<span class="starredname">/', '/<\/span><\/span>/', '/<\/span>/'),
					array('«', '', '»'),
					$name
				);
				$name = strip_tags($name);
				if (!empty($attrs['truncate'])) {
					if (mb_strlen($name) > $attrs['truncate']) {
						$name  = preg_replace("/\(.*\) ?/", '', $name); //removes () and text inbetween - what about ", [ and { etc?
						$words = preg_split('/[, -]+/', $name); // names separated with space, comma or hyphen - any others?
						$name  = $words[count($words) - 1];
						for ($i = count($words) - 2; $i >= 0; $i--) {
							$len = mb_strlen($name);
							for ($j = count($words) - 3; $j >= 0; $j--) {
								$len += mb_strlen($words[$j]);
							}
							if ($len > $attrs['truncate']) {
								$first_letter = mb_substr($words[$i], 0, 1);
								// Do not show " of nick-names
								if ($first_letter != "\"") {
									$name = mb_substr($words[$i], 0, 1) . '. ' . $name;
								}
							} else {
								$name = $words[$i] . ' ' . $name;
							}
						}
					}
				} else {
					$addname = $record->getAddName();
					$addname = preg_replace(
						array('/<span class="starredname">/', '/<\/span><\/span>/', '/<\/span>/'),
						array('«', '', '»'),
						$addname
					);
					$addname = strip_tags($addname);
					if (!empty($addname)) {
						$name .= " " . $addname;
					}
				}
				$this->current_element->addText(trim($name));
			}
		}
	}

	/**
	 * XML <GedcomValue/>
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function gedcomValueStartHandler($attrs) {
		global $WT_TREE;

		$id    = "";
		$match = array();
		if (preg_match("/0 @(.+)@/", $this->gedrec, $match)) {
			$id = $match[1];
		}

		if (isset($attrs['newline']) && $attrs['newline'] == "1") {
			$useBreak = "1";
		} else {
			$useBreak = "0";
		}

		$tag = $attrs['tag'];
		if (!empty($tag)) {
			if ($tag == "@desc") {
				$value = $this->desc;
				$value = trim($value);
				$this->current_element->addText($value);
			}
			if ($tag == "@id") {
				$this->current_element->addText($id);
			} else {
				$tag = str_replace("@fact", $this->fact, $tag);
				if (empty($attrs['level'])) {
					$temp  = explode(" ", trim($this->gedrec));
					$level = $temp[0];
					if ($level == 0) {
						$level++;
					}
				} else {
					$level = $attrs['level'];
				}
				$tags  = preg_split('/[: ]/', $tag);
				$value = $this->getGedcomValue($tag, $level, $this->gedrec);
				switch (end($tags)) {
					case 'DATE':
						$tmp   = new Date($value);
						$value = $tmp->display();
						break;
					case 'PLAC':
						$tmp   = new Place($value, $WT_TREE);
						$value = $tmp->getShortName();
						break;
				}
				if ($useBreak == "1") {
					// Insert <br> when multiple dates exist.
					// This works around a TCPDF bug that incorrectly wraps RTL dates on LTR pages
					$value = str_replace('(', '<br>(', $value);
					$value = str_replace('<span dir="ltr"><br>', '<br><span dir="ltr">', $value);
					$value = str_replace('<span dir="rtl"><br>', '<br><span dir="rtl">', $value);
					if (substr($value, 0, 6) == '<br>') {
						$value = substr($value, 6);
					}
				}
				$tmp = explode(':', $tag);
				if (in_array(end($tmp),  array('NOTE', 'TEXT'))) {
					$value = Filter::formatText($value, $WT_TREE); // We'll strip HTML in addText()
				}
				$this->current_element->addText($value);
			}
		}
	}

	/**
	 * XML <RepeatTag>
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function repeatTagStartHandler($attrs) {
		$this->process_repeats++;
		if ($this->process_repeats > 1) {
			return;
		}

		array_push($this->repeats_stack, array($this->repeats, $this->repeat_bytes));
		$this->repeats      = array();
		$this->repeat_bytes = xml_get_current_line_number($this->parser);

		$tag = "";
		if (isset($attrs['tag'])) {
			$tag = $attrs['tag'];
		}
		if (!empty($tag)) {
			if ($tag == "@desc") {
				$value = $this->desc;
				$value = trim($value);
				$this->current_element->addText($value);
			} else {
				$tag   = str_replace("@fact", $this->fact, $tag);
				$tags  = explode(":", $tag);
				$temp  = explode(" ", trim($this->gedrec));
				$level = $temp[0];
				if ($level == 0) {
					$level++;
				}
				$subrec = $this->gedrec;
				$t      = $tag;
				$count  = count($tags);
				$i      = 0;
				while ($i < $count) {
					$t = $tags[$i];
					if (!empty($t)) {
						if ($i < ($count - 1)) {
							$subrec = Functions::getSubRecord($level, "$level $t", $subrec);
							if (empty($subrec)) {
								$level--;
								$subrec = Functions::getSubRecord($level, "@ $t", $this->gedrec);
								if (empty($subrec)) {
									return;
								}
							}
						}
						$level++;
					}
					$i++;
				}
				$level--;
				$count = preg_match_all("/$level $t(.*)/", $subrec, $match, PREG_SET_ORDER);
				$i     = 0;
				while ($i < $count) {
					$this->repeats[] = Functions::getSubRecord($level, "$level $t", $subrec, $i + 1);
					$i++;
				}
			}
		}
	}

	/**
	 * XML </ RepeatTag>
	 */
	private function repeatTagEndHandler() {
		global $report;

		$this->process_repeats--;
		if ($this->process_repeats > 0) {
			return;
		}

		// Check if there is anything to repeat
		if (count($this->repeats) > 0) {
			// No need to load them if not used...

			$lineoffset = 0;
			foreach ($this->repeats_stack as $rep) {
				$lineoffset += $rep[1];
			}
			//-- read the xml from the file
			$lines = file($report);
			while (strpos($lines[$lineoffset + $this->repeat_bytes], "<RepeatTag") === false) {
				$lineoffset--;
			}
			$lineoffset++;
			$reportxml = "<tempdoc>\n";
			$line_nr   = $lineoffset + $this->repeat_bytes;
			// RepeatTag Level counter
			$count = 1;
			while (0 < $count) {
				if (strstr($lines[$line_nr], "<RepeatTag") !== false) {
					$count++;
				} elseif (strstr($lines[$line_nr], "</RepeatTag") !== false) {
					$count--;
				}
				if (0 < $count) {
					$reportxml .= $lines[$line_nr];
				}
				$line_nr++;
			}
			// No need to drag this
			unset($lines);
			$reportxml .= "</tempdoc>\n";
			// Save original values
			array_push($this->parser_stack, $this->parser);
			$oldgedrec = $this->gedrec;
			foreach ($this->repeats as $gedrec) {
				$this->gedrec  = $gedrec;
				$repeat_parser = xml_parser_create();
				$this->parser  = $repeat_parser;
				xml_parser_set_option($repeat_parser, XML_OPTION_CASE_FOLDING, false);
				xml_set_element_handler($repeat_parser, array($this, 'startElement'), array($this, 'endElement'));
				xml_set_character_data_handler($repeat_parser, array($this, 'characterData'));
				if (!xml_parse($repeat_parser, $reportxml, true)) {
					throw new \DomainException(sprintf(
						'RepeatTagEHandler XML error: %s at line %d',
						xml_error_string(xml_get_error_code($repeat_parser)),
						xml_get_current_line_number($repeat_parser)
					));
				}
				xml_parser_free($repeat_parser);
			}
			// Restore original values
			$this->gedrec = $oldgedrec;
			$this->parser = array_pop($this->parser_stack);
		}
		list($this->repeats, $this->repeat_bytes) = array_pop($this->repeats_stack);
	}

	/**
	 * Variable lookup
	 *
	 * Retrieve predefined variables :
	 *
	 * @ desc GEDCOM fact description, example:
	 *        1 EVEN This is a description
	 * @ fact GEDCOM fact tag, such as BIRT, DEAT etc.
	 * $ I18N::translate('....')
	 * $ language_settings[]
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function varStartHandler($attrs) {
		if (empty($attrs['var'])) {
			throw new \DomainException('REPORT ERROR var: The attribute "var=" is missing or not set in the XML file on line: ' . xml_get_current_line_number($this->parser));
		}

		$var = $attrs['var'];
		// SetVar element preset variables
		if (!empty($this->vars[$var]['id'])) {
			$var = $this->vars[$var]['id'];
		} else {
			$tfact = $this->fact;
			if (($this->fact === "EVEN" || $this->fact === "FACT") && $this->type !== " ") {
				// Use :
				// n TYPE This text if string
				$tfact = $this->type;
			}
			$var = str_replace(array("@fact", "@desc"), array(GedcomTag::getLabel($tfact), $this->desc), $var);
			if (preg_match('/^I18N::number\((.+)\)$/', $var, $match)) {
				$var = I18N::number($match[1]);
			} elseif (preg_match('/^I18N::translate\(\'(.+)\'\)$/', $var, $match)) {
				$var = I18N::translate($match[1]);
			} elseif (preg_match('/^I18N::translateContext\(\'(.+)\', *\'(.+)\'\)$/', $var, $match)) {
				$var = I18N::translateContext($match[1], $match[2]);
			}
		}
		// Check if variable is set as a date and reformat the date
		if (isset($attrs['date'])) {
			if ($attrs['date'] === "1") {
				$g   = new Date($var);
				$var = $g->display();
			}
		}
		$this->current_element->addText($var);
		$this->text = $var; // Used for title/descriptio
	}

	/**
	 * XML <Facts>
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function factsStartHandler($attrs) {
		global $WT_TREE;

		$this->process_repeats++;
		if ($this->process_repeats > 1) {
			return;
		}

		array_push($this->repeats_stack, array($this->repeats, $this->repeat_bytes));
		$this->repeats      = array();
		$this->repeat_bytes = xml_get_current_line_number($this->parser);

		$id    = "";
		$match = array();
		if (preg_match("/0 @(.+)@/", $this->gedrec, $match)) {
			$id = $match[1];
		}
		$tag = "";
		if (isset($attrs['ignore'])) {
			$tag .= $attrs['ignore'];
		}
		if (preg_match("/\\$(.+)/", $tag, $match)) {
			$tag = $this->vars[$match[1]]['id'];
		}

		$record = GedcomRecord::getInstance($id, $WT_TREE);
		if (empty($attrs['diff']) && !empty($id)) {
			$facts = $record->getFacts();
			Functions::sortFacts($facts);
			$this->repeats  = array();
			$nonfacts       = explode(',', $tag);
			foreach ($facts as $event) {
				if (!in_array($event->getTag(), $nonfacts)) {
					$this->repeats[] = $event->getGedcom();
				}
			}
		} else {
			foreach ($record->getFacts() as $fact) {
				if ($fact->isPendingAddition() && $fact->getTag() !== 'CHAN') {
					$this->repeats[] = $fact->getGedcom();
				}
			}
		}
	}

	/**
	 * XML </Facts>
	 */
	private function factsEndHandler() {
		global $report;

		$this->process_repeats--;
		if ($this->process_repeats > 0) {
			return;
		}

		// Check if there is anything to repeat
		if (count($this->repeats) > 0) {

			$line       = xml_get_current_line_number($this->parser) - 1;
			$lineoffset = 0;
			foreach ($this->repeats_stack as $rep) {
				$lineoffset += $rep[1];
			}

			//-- read the xml from the file
			$lines = file($report);
			while ($lineoffset + $this->repeat_bytes > 0 && strpos($lines[$lineoffset + $this->repeat_bytes], '<Facts ') === false) {
				$lineoffset--;
			}
			$lineoffset++;
			$reportxml = "<tempdoc>\n";
			$i         = $line + $lineoffset;
			$line_nr   = $this->repeat_bytes + $lineoffset;
			while ($line_nr < $i) {
				$reportxml .= $lines[$line_nr];
				$line_nr++;
			}
			// No need to drag this
			unset($lines);
			$reportxml .= "</tempdoc>\n";
			// Save original values
			array_push($this->parser_stack, $this->parser);
			$oldgedrec = $this->gedrec;
			$count     = count($this->repeats);
			$i         = 0;
			while ($i < $count) {
				$this->gedrec = $this->repeats[$i];
				$this->fact   = '';
				$this->desc   = '';
				if (preg_match('/1 (\w+)(.*)/', $this->gedrec, $match)) {
					$this->fact = $match[1];
					if ($this->fact === 'EVEN' || $this->fact === 'FACT') {
						$tmatch = array();
						if (preg_match('/2 TYPE (.+)/', $this->gedrec, $tmatch)) {
							$this->type = trim($tmatch[1]);
						} else {
							$this->type = ' ';
						}
					}
					$this->desc = trim($match[2]);
					$this->desc .= Functions::getCont(2, $this->gedrec);
				}
				$repeat_parser = xml_parser_create();
				$this->parser  = $repeat_parser;
				xml_parser_set_option($repeat_parser, XML_OPTION_CASE_FOLDING, false);
				xml_set_element_handler($repeat_parser, array($this, 'startElement'), array($this, 'endElement'));
				xml_set_character_data_handler($repeat_parser, array($this, 'characterData'));
				if (!xml_parse($repeat_parser, $reportxml, true)) {
					throw new \DomainException(sprintf(
						'FactsEHandler XML error: %s at line %d',
						xml_error_string(xml_get_error_code($repeat_parser)),
						xml_get_current_line_number($repeat_parser)
					));
				}
				xml_parser_free($repeat_parser);
				$i++;
			}
			// Restore original values
			$this->parser = array_pop($this->parser_stack);
			$this->gedrec = $oldgedrec;
		}
		list($this->repeats, $this->repeat_bytes) = array_pop($this->repeats_stack);
	}

	/**
	 * Setting upp or changing variables in the XML
	 * The XML variable name and value is stored in $this->vars
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function setVarStartHandler($attrs) {
		if (empty($attrs['name'])) {
			throw new \DomainException('REPORT ERROR var: The attribute "name" is missing or not set in the XML file');
		}

		$name  = $attrs['name'];
		$value = $attrs['value'];
		$match = array();
		// Current GEDCOM record strings
		if ($value == "@ID") {
			if (preg_match("/0 @(.+)@/", $this->gedrec, $match)) {
				$value = $match[1];
			}
		} elseif ($value == "@fact") {
			$value = $this->fact;
		} elseif ($value == "@desc") {
			$value = $this->desc;
		} elseif ($value == "@generation") {
			$value = $this->generation;
		} elseif (preg_match("/@(\w+)/", $value, $match)) {
			$gmatch = array();
			if (preg_match("/\d $match[1] (.+)/", $this->gedrec, $gmatch)) {
				$value = str_replace("@", "", trim($gmatch[1]));
			}
		}
		if (preg_match("/\\$(\w+)/", $name, $match)) {
			$name = $this->vars["'" . $match[1] . "'"]['id'];
		}
		$count = preg_match_all("/\\$(\w+)/", $value, $match, PREG_SET_ORDER);
		$i     = 0;
		while ($i < $count) {
			$t     = $this->vars[$match[$i][1]]['id'];
			$value = preg_replace("/\\$" . $match[$i][1] . "/", $t, $value, 1);
			$i++;
		}
		if (preg_match('/^I18N::number\((.+)\)$/', $value, $match)) {
			$value = I18N::number($match[1]);
		} elseif (preg_match('/^I18N::translate\(\'(.+)\'\)$/', $value, $match)) {
			$value = I18N::translate($match[1]);
		} elseif (preg_match('/^I18N::translateContext\(\'(.+)\', *\'(.+)\'\)$/', $value, $match)) {
			$value = I18N::translateContext($match[1], $match[2]);
		}
		// Arithmetic functions
		if (preg_match("/(\d+)\s*([\-\+\*\/])\s*(\d+)/", $value, $match)) {
			switch ($match[2]) {
				case "+":
					$t     = $match[1] + $match[3];
					$value = preg_replace("/" . $match[1] . "\s*([\-\+\*\/])\s*" . $match[3] . "/", $t, $value);
					break;
				case "-":
					$t     = $match[1] - $match[3];
					$value = preg_replace("/" . $match[1] . "\s*([\-\+\*\/])\s*" . $match[3] . "/", $t, $value);
					break;
				case "*":
					$t     = $match[1] * $match[3];
					$value = preg_replace("/" . $match[1] . "\s*([\-\+\*\/])\s*" . $match[3] . "/", $t, $value);
					break;
				case "/":
					$t     = $match[1] / $match[3];
					$value = preg_replace("/" . $match[1] . "\s*([\-\+\*\/])\s*" . $match[3] . "/", $t, $value);
					break;
			}
		}
		if (strpos($value, "@") !== false) {
			$value = "";
		}
		$this->vars[$name]['id'] = $value;
	}

	/**
	 * XML <if > start element
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function ifStartHandler($attrs) {
		if ($this->process_ifs > 0) {
			$this->process_ifs++;

			return;
		}

		$condition = $attrs['condition'];
		$condition = $this->substituteVars($condition, true);
		$condition = str_replace(array(" LT ", " GT "), array("<", ">"), $condition);
		// Replace the first accurance only once of @fact:DATE or in any other combinations to the current fact, such as BIRT
		$condition = str_replace("@fact", $this->fact, $condition);
		$match     = array();
		$count     = preg_match_all("/@([\w:\.]+)/", $condition, $match, PREG_SET_ORDER);
		$i         = 0;
		while ($i < $count) {
			$id    = $match[$i][1];
			$value = '""';
			if ($id == "ID") {
				if (preg_match("/0 @(.+)@/", $this->gedrec, $match)) {
					$value = "'" . $match[1] . "'";
				}
			} elseif ($id === "fact") {
				$value = '"' . $this->fact . '"';
			} elseif ($id === "desc") {
				$value = '"' . addslashes($this->desc) . '"';
			} elseif ($id === "generation") {
				$value = '"' . $this->generation . '"';
			} else {

				$temp  = explode(" ", trim($this->gedrec));
				$level = $temp[0];
				if ($level == 0) {
					$level++;
				}
				$value = $this->getGedcomValue($id, $level, $this->gedrec);
				if (empty($value)) {
					$level++;
					$value = $this->getGedcomValue($id, $level, $this->gedrec);
				}
				$value = preg_replace("/^@(" . WT_REGEX_XREF . ")@$/", "$1", $value);
				$value = "\"" . addslashes($value) . "\"";
			}
			$condition = str_replace("@$id", $value, $condition);
			$i++;
		}
		$condition = "return (bool) ($condition);";
		$ret       = @eval($condition);
		if (!$ret) {
			$this->process_ifs++;
		}
	}

	/**
	 * XML <if /> end element
	 */
	private function ifEndHandler() {
		if ($this->process_ifs > 0) {
			$this->process_ifs--;
		}
	}

	/**
	 * XML <Footnote > start element
	 * Collect the Footnote links
	 * GEDCOM Records that are protected by Privacy setting will be ignore
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function footnoteStartHandler($attrs) {
		global $WT_TREE;

		$id = "";
		if (preg_match("/[0-9] (.+) @(.+)@/", $this->gedrec, $match)) {
			$id = $match[2];
		}
		$record = GedcomRecord::GetInstance($id, $WT_TREE);
		if ($record && $record->canShow()) {
			array_push($this->print_data_stack, $this->print_data);
			$this->print_data = true;
			$style            = "";
			if (!empty($attrs['style'])) {
				$style = $attrs['style'];
			}
			$this->footnote_element = $this->current_element;
			$this->current_element  = $this->report_root->createFootnote($style);
		} else {
			$this->print_data       = false;
			$this->process_footnote = false;
		}
	}

	/**
	 * XML <Footnote /> end element
	 * Print the collected Footnote data
	 */
	private function footnoteEndHandler() {
		if ($this->process_footnote) {
			$this->print_data = array_pop($this->print_data_stack);
			$temp             = trim($this->current_element->getValue());
			if (strlen($temp) > 3) {
				$this->wt_report->addElement($this->current_element);
			}
			$this->current_element = $this->footnote_element;
		} else {
			$this->process_footnote = true;
		}
	}

	/**
	 * XML <FootnoteTexts /> element
	 */
	private function footnoteTextsStartHandler() {
		$temp = "footnotetexts";
		$this->wt_report->addElement($temp);
	}

	/**
	 * XML <AgeAtDeath /> element handler
	 */
	private function ageAtDeathStartHandler() {
		// This duplicates functionality in FunctionsPrint::format_fact_date()
		global $factrec, $WT_TREE;

		$match = array();
		if (preg_match("/0 @(.+)@/", $this->gedrec, $match)) {
			$person = Individual::getInstance($match[1], $WT_TREE);
			// Recorded age
			if (preg_match('/\n2 AGE (.+)/', $factrec, $match)) {
				$fact_age = $match[1];
			} else {
				$fact_age = '';
			}
			if (preg_match('/\n2 HUSB\n3 AGE (.+)/', $factrec, $match)) {
				$husb_age = $match[1];
			} else {
				$husb_age = '';
			}
			if (preg_match('/\n2 WIFE\n3 AGE (.+)/', $factrec, $match)) {
				$wife_age = $match[1];
			} else {
				$wife_age = '';
			}

			// Calculated age
			$birth_date = $person->getBirthDate();
			// Can't use getDeathDate(), as this also gives BURI/CREM events, which
			// wouldn't give the correct "days after death" result for people with
			// no DEAT.
			$death_event = $person->getFirstFact('DEAT');
			if ($death_event) {
				$death_date = $death_event->getDate();
			} else {
				$death_date = new Date('');
			}
			$value = '';
			if (Date::compare($birth_date, $death_date) <= 0 || !$person->isDead()) {
				$age = Date::getAgeGedcom($birth_date, $death_date);
				// Only show calculated age if it differs from recorded age
				if ($age != '' && $age != "0d") {
					if ($fact_age != '' && $fact_age != $age || $fact_age == '' && $husb_age == '' && $wife_age == '' || $husb_age != '' && $person->getSex() == 'M' && $husb_age != $age || $wife_age != '' && $person->getSex() == 'F' && $wife_age != $age
					) {
						$value  = FunctionsDate::getAgeAtEvent($age, false);
						$abbrev = substr($value, 0, strpos($value, ' ') + 5);
						if ($value !== $abbrev) {
							$value = $abbrev . '.';
						}
					}
				}
			}
			$this->current_element->addText($value);
		}
	}

	/**
	 * XML element Forced line break handler - HTML code
	 */
	private function brStartHandler() {
		if ($this->print_data && $this->process_gedcoms === 0) {
			$this->current_element->addText('<br>');
		}
	}

	/**
	 * XML <sp />element Forced space handler
	 */
	private function spStartHandler() {
		if ($this->print_data && $this->process_gedcoms === 0) {
			$this->current_element->addText(' ');
		}
	}

	/**
	 * XML <HighlightedImage/>
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function highlightedImageStartHandler($attrs) {
		global $WT_TREE;

		$id    = '';
		$match = array();
		if (preg_match("/0 @(.+)@/", $this->gedrec, $match)) {
			$id = $match[1];
		}

		// mixed Position the top corner of this box on the page. the default is the current position
		$top = '.';
		if (isset($attrs['top'])) {
			if ($attrs['top'] === '0') {
				$top = 0;
			} elseif ($attrs['top'] === '.') {
				$top = '.';
			} elseif (!empty($attrs['top'])) {
				$top = (int) $attrs['top'];
			}
		}

		// mixed Position the left corner of this box on the page. the default is the current position
		$left = '.';
		if (isset($attrs['left'])) {
			if ($attrs['left'] === '0') {
				$left = 0;
			} elseif ($attrs['left'] === '.') {
				$left = '.';
			} elseif (!empty($attrs['left'])) {
				$left = (int) $attrs['left'];
			}
		}

		// string Align the image in left, center, right
		$align = '';
		if (!empty($attrs['align'])) {
			$align = $attrs['align'];
		}

		// string Next Line should be T:next to the image, N:next line
		$ln = '';
		if (!empty($attrs['ln'])) {
			$ln = $attrs['ln'];
		}

		$width  = 0;
		$height = 0;
		if (!empty($attrs['width'])) {
			$width = (int) $attrs['width'];
		}
		if (!empty($attrs['height'])) {
			$height = (int) $attrs['height'];
		}

		$person      = Individual::getInstance($id, $WT_TREE);
		$mediaobject = $person->findHighlightedMedia();
		if ($mediaobject) {
			$attributes = $mediaobject->getImageAttributes('thumb');
			if (in_array(
					$attributes['ext'],
					array(
						'GIF',
						'JPG',
						'PNG',
						'SWF',
						'PSD',
						'BMP',
						'TIFF',
						'TIFF',
						'JPC',
						'JP2',
						'JPX',
						'JB2',
						'SWC',
						'IFF',
						'WBMP',
						'XBM',
					)
				) && $mediaobject->canShow() && $mediaobject->fileExists('thumb')
			) {
				if ($width > 0 && $height == 0) {
					$perc   = $width / $attributes['adjW'];
					$height = round($attributes['adjH'] * $perc);
				} elseif ($height > 0 && $width == 0) {
					$perc  = $height / $attributes['adjH'];
					$width = round($attributes['adjW'] * $perc);
				} else {
					$width  = $attributes['adjW'];
					$height = $attributes['adjH'];
				}
				$image = $this->report_root->createImageFromObject($mediaobject, $left, $top, $width, $height, $align, $ln);
				$this->wt_report->addElement($image);
			}
		}
	}

	/**
	 * XML <Image/>
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function imageStartHandler($attrs) {
		global $WT_TREE;

		// mixed Position the top corner of this box on the page. the default is the current position
		$top = '.';
		if (isset($attrs['top'])) {
			if ($attrs['top'] === "0") {
				$top = 0;
			} elseif ($attrs['top'] === '.') {
				$top = '.';
			} elseif (!empty($attrs['top'])) {
				$top = (int) $attrs['top'];
			}
		}

		// mixed Position the left corner of this box on the page. the default is the current position
		$left = '.';
		if (isset($attrs['left'])) {
			if ($attrs['left'] === '0') {
				$left = 0;
			} elseif ($attrs['left'] === '.') {
				$left = '.';
			} elseif (!empty($attrs['left'])) {
				$left = (int) $attrs['left'];
			}
		}

		// string Align the image in left, center, right
		$align = '';
		if (!empty($attrs['align'])) {
			$align = $attrs['align'];
		}

		// string Next Line should be T:next to the image, N:next line
		$ln = 'T';
		if (!empty($attrs['ln'])) {
			$ln = $attrs['ln'];
		}

		$width  = 0;
		$height = 0;
		if (!empty($attrs['width'])) {
			$width = (int) $attrs['width'];
		}
		if (!empty($attrs['height'])) {
			$height = (int) $attrs['height'];
		}

		$file = '';
		if (!empty($attrs['file'])) {
			$file = $attrs['file'];
		}
		if ($file == "@FILE") {
			$match = array();
			if (preg_match("/\d OBJE @(.+)@/", $this->gedrec, $match)) {
				$mediaobject = Media::getInstance($match[1], $WT_TREE);
				$attributes  = $mediaobject->getImageAttributes('thumb');
				if (in_array(
						$attributes['ext'],
						array(
							'GIF',
							'JPG',
							'PNG',
							'SWF',
							'PSD',
							'BMP',
							'TIFF',
							'TIFF',
							'JPC',
							'JP2',
							'JPX',
							'JB2',
							'SWC',
							'IFF',
							'WBMP',
							'XBM',
						)
					) && $mediaobject->canShow() && $mediaobject->fileExists('thumb')
				) {
					if ($width > 0 && $height == 0) {
						$perc   = $width / $attributes['adjW'];
						$height = round($attributes['adjH'] * $perc);
					} elseif ($height > 0 && $width == 0) {
						$perc  = $height / $attributes['adjH'];
						$width = round($attributes['adjW'] * $perc);
					} else {
						$width  = $attributes['adjW'];
						$height = $attributes['adjH'];
					}
					$image = $this->report_root->createImageFromObject($mediaobject, $left, $top, $width, $height, $align, $ln);
					$this->wt_report->addElement($image);
				}
			}
		} else {
			if (file_exists($file) && preg_match("/(jpg|jpeg|png|gif)$/i", $file)) {
				$size = getimagesize($file);
				if ($width > 0 && $height == 0) {
					$perc   = $width / $size[0];
					$height = round($size[1] * $perc);
				} elseif ($height > 0 && $width == 0) {
					$perc  = $height / $size[1];
					$width = round($size[0] * $perc);
				} else {
					$width  = $size[0];
					$height = $size[1];
				}
				$image = $this->report_root->createImage($file, $left, $top, $width, $height, $align, $ln);
				$this->wt_report->addElement($image);
			}
		}
	}

	/**
	 * XML <Line> element handler
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function lineStartHandler($attrs) {
		// Start horizontal position, current position (default)
		$x1 = ".";
		if (isset($attrs['x1'])) {
			if ($attrs['x1'] === "0") {
				$x1 = 0;
			} elseif ($attrs['x1'] === ".") {
				$x1 = ".";
			} elseif (!empty($attrs['x1'])) {
				$x1 = (int) $attrs['x1'];
			}
		}
		// Start vertical position, current position (default)
		$y1 = ".";
		if (isset($attrs['y1'])) {
			if ($attrs['y1'] === "0") {
				$y1 = 0;
			} elseif ($attrs['y1'] === ".") {
				$y1 = ".";
			} elseif (!empty($attrs['y1'])) {
				$y1 = (int) $attrs['y1'];
			}
		}
		// End horizontal position, maximum width (default)
		$x2 = ".";
		if (isset($attrs['x2'])) {
			if ($attrs['x2'] === "0") {
				$x2 = 0;
			} elseif ($attrs['x2'] === ".") {
				$x2 = ".";
			} elseif (!empty($attrs['x2'])) {
				$x2 = (int) $attrs['x2'];
			}
		}
		// End vertical position
		$y2 = ".";
		if (isset($attrs['y2'])) {
			if ($attrs['y2'] === "0") {
				$y2 = 0;
			} elseif ($attrs['y2'] === ".") {
				$y2 = ".";
			} elseif (!empty($attrs['y2'])) {
				$y2 = (int) $attrs['y2'];
			}
		}

		$line = $this->report_root->createLine($x1, $y1, $x2, $y2);
		$this->wt_report->addElement($line);
	}

	/**
	 * XML <List>
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function listStartHandler($attrs) {
		global $WT_TREE;

		$this->process_repeats++;
		if ($this->process_repeats > 1) {
			return;
		}

		$match = array();
		if (isset($attrs['sortby'])) {
			$sortby = $attrs['sortby'];
			if (preg_match("/\\$(\w+)/", $sortby, $match)) {
				$sortby = $this->vars[$match[1]]['id'];
				$sortby = trim($sortby);
			}
		} else {
			$sortby = "NAME";
		}

		if (isset($attrs['list'])) {
			$listname = $attrs['list'];
		} else {
			$listname = "individual";
		}
		// Some filters/sorts can be applied using SQL, while others require PHP
		switch ($listname) {
			case "pending":
				$rows = Database::prepare(
					"SELECT xref, CASE new_gedcom WHEN '' THEN old_gedcom ELSE new_gedcom END AS gedcom" .
					" FROM `##change`" . " WHERE (xref, change_id) IN (" .
					"  SELECT xref, MAX(change_id)" .
					"  FROM `##change`" .
					"  WHERE status = 'pending' AND gedcom_id = :tree_id" .
					"  GROUP BY xref" .
					" )"
				)->execute(array(
					'tree_id' => $WT_TREE->getTreeId(),
				))->fetchAll();
				$this->list = array();
				foreach ($rows as $row) {
					$this->list[] = GedcomRecord::getInstance($row->xref, $WT_TREE, $row->gedcom);
				}
				break;
			case 'individual':
				$sql_select   = "SELECT DISTINCT i_id AS xref, i_gedcom AS gedcom FROM `##individuals` ";
				$sql_join     = "";
				$sql_where    = " WHERE i_file = :tree_id";
				$sql_order_by = "";
				$sql_params   = array('tree_id' => $WT_TREE->getTreeId());
				foreach ($attrs as $attr => $value) {
					if (strpos($attr, 'filter') === 0 && $value) {
						$value = $this->substituteVars($value, false);
						// Convert the various filters into SQL
						if (preg_match('/^(\w+):DATE (LTE|GTE) (.+)$/', $value, $match)) {
							$sql_join .= " JOIN `##dates` AS {$attr} ON ({$attr}.d_file=i_file AND {$attr}.d_gid=i_id)";
							$sql_where .= " AND {$attr}.d_fact = :{$attr}fact";
							$sql_params[$attr . 'fact'] = $match[1];
							$date                       = new Date($match[3]);
							if ($match[2] == "LTE") {
								$sql_where .= " AND {$attr}.d_julianday2 <= :{$attr}date";
								$sql_params[$attr . 'date'] = $date->maximumJulianDay();
							} else {
								$sql_where .= " AND {$attr}.d_julianday1 >= :{$attr}date";
								$sql_params[$attr . 'date'] = $date->minimumJulianDay();
							}
							if ($sortby == $match[1]) {
								$sortby = "";
								$sql_order_by .= ($sql_order_by ? ", " : " ORDER BY ") . "{$attr}.d_julianday1";
							}
							unset($attrs[$attr]); // This filter has been fully processed
						} elseif (preg_match('/^NAME CONTAINS (.*)$/', $value, $match)) {
							// Do nothing, unless you have to
							if ($match[1] != '' || $sortby == 'NAME') {
								$sql_join .= " JOIN `##name` AS {$attr} ON (n_file=i_file AND n_id=i_id)";
								// Search the DB only if there is any name supplied
								if ($match[1] != "") {
									$names = explode(" ", $match[1]);
									foreach ($names as $n => $name) {
										$sql_where .= " AND {$attr}.n_full LIKE CONCAT('%', :{$attr}name{$n}, '%')";
										$sql_params[$attr . 'name' . $n] = $name;
									}
								}
								// Let the DB do the name sorting even when no name was entered
								if ($sortby == "NAME") {
									$sortby = "";
									$sql_order_by .= ($sql_order_by ? ", " : " ORDER BY ") . "{$attr}.n_sort";
								}
							}
							unset($attrs[$attr]); // This filter has been fully processed
						} elseif (preg_match('/^REGEXP \/(.+)\//', $value, $match)) {
							$sql_where .= " AND i_gedcom REGEXP :{$attr}gedcom";
							// PDO helpfully escapes backslashes for us, preventing us from matching "\n1 FACT"
							$sql_params[$attr . 'gedcom'] = str_replace('\n', "\n", $match[1]);
							unset($attrs[$attr]); // This filter has been fully processed
						} elseif (preg_match('/^(?:\w+):PLAC CONTAINS (.+)$/', $value, $match)) {
							$sql_join .= " JOIN `##places` AS {$attr}a ON ({$attr}a.p_file = i_file)";
							$sql_join .= " JOIN `##placelinks` AS {$attr}b ON ({$attr}a.p_file = {$attr}b.pl_file AND {$attr}b.pl_p_id = {$attr}a.p_id AND {$attr}b.pl_gid = i_id)";
							$sql_where .= " AND {$attr}a.p_place LIKE CONCAT('%', :{$attr}place, '%')";
							$sql_params[$attr . 'place'] = $match[1];
							// Don't unset this filter. This is just initial filtering
						} elseif (preg_match('/^(\w*):*(\w*) CONTAINS (.+)$/', $value, $match)) {
							$sql_where .= " AND i_gedcom LIKE CONCAT('%', :{$attr}contains1, '%', :{$attr}contains2, '%', :{$attr}contains3, '%')";
							$sql_params[$attr . 'contains1'] = $match[1];
							$sql_params[$attr . 'contains2'] = $match[2];
							$sql_params[$attr . 'contains3'] = $match[3];
							// Don't unset this filter. This is just initial filtering
						}
					}
				}

				$this->list = array();
				$rows       = Database::prepare(
					$sql_select . $sql_join . $sql_where . $sql_order_by
				)->execute($sql_params)->fetchAll();

				foreach ($rows as $row) {
					$this->list[] = Individual::getInstance($row->xref, $WT_TREE, $row->gedcom);
				}
				break;

			case 'family':
				$sql_select   = "SELECT DISTINCT f_id AS xref, f_gedcom AS gedcom FROM `##families`";
				$sql_join     = "";
				$sql_where    = " WHERE f_file = :tree_id";
				$sql_order_by = "";
				$sql_params   = array('tree_id' => $WT_TREE->getTreeId());
				foreach ($attrs as $attr => $value) {
					if (strpos($attr, 'filter') === 0 && $value) {
						$value = $this->substituteVars($value, false);
						// Convert the various filters into SQL
						if (preg_match('/^(\w+):DATE (LTE|GTE) (.+)$/', $value, $match)) {
							$sql_join .= " JOIN `##dates` AS {$attr} ON ({$attr}.d_file=f_file AND {$attr}.d_gid=f_id)";
							$sql_where .= " AND {$attr}.d_fact = :{$attr}fact";
							$sql_params[$attr . 'fact'] = $match[1];
							$date                       = new Date($match[3]);
							if ($match[2] == "LTE") {
								$sql_where .= " AND {$attr}.d_julianday2 <= :{$attr}date";
								$sql_params[$attr . 'date'] = $date->maximumJulianDay();
							} else {
								$sql_where .= " AND {$attr}.d_julianday1 >= :{$attr}date";
								$sql_params[$attr . 'date'] = $date->minimumJulianDay();
							}
							if ($sortby == $match[1]) {
								$sortby = "";
								$sql_order_by .= ($sql_order_by ? ", " : " ORDER BY ") . "{$attr}.d_julianday1";
							}
							unset($attrs[$attr]); // This filter has been fully processed
						} elseif (preg_match('/^REGEXP \/(.+)\//', $value, $match)) {
							$sql_where .= " AND f_gedcom REGEXP :{$attr}gedcom";
							// PDO helpfully escapes backslashes for us, preventing us from matching "\n1 FACT"
							$sql_params[$attr . 'gedcom'] = str_replace('\n', "\n", $match[1]);
							unset($attrs[$attr]); // This filter has been fully processed
						} elseif (preg_match('/^NAME CONTAINS (.+)$/', $value, $match)) {
							// Do nothing, unless you have to
							if ($match[1] != '' || $sortby == 'NAME') {
								$sql_join .= " JOIN `##name` AS {$attr} ON n_file = f_file AND n_id IN (f_husb, f_wife)";
								// Search the DB only if there is any name supplied
								if ($match[1] != "") {
									$names = explode(" ", $match[1]);
									foreach ($names as $n => $name) {
										$sql_where .= " AND {$attr}.n_full LIKE CONCAT('%', :{$attr}name{$n}, '%')";
										$sql_params[$attr . 'name' . $n] = $name;
									}
								}
								// Let the DB do the name sorting even when no name was entered
								if ($sortby == "NAME") {
									$sortby = "";
									$sql_order_by .= ($sql_order_by ? ", " : " ORDER BY ") . "{$attr}.n_sort";
								}
							}
							unset($attrs[$attr]); // This filter has been fully processed

						} elseif (preg_match('/^(?:\w+):PLAC CONTAINS (.+)$/', $value, $match)) {
							$sql_join .= " JOIN `##places` AS {$attr}a ON ({$attr}a.p_file=f_file)";
							$sql_join .= " JOIN `##placelinks` AS {$attr}b ON ({$attr}a.p_file={$attr}b.pl_file AND {$attr}b.pl_p_id={$attr}a.p_id AND {$attr}b.pl_gid=f_id)";
							$sql_where .= " AND {$attr}a.p_place LIKE CONCAT('%', :{$attr}place, '%')";
							$sql_params[$attr . 'place'] = $match[1];
							// Don't unset this filter. This is just initial filtering
						} elseif (preg_match('/^(\w*):*(\w*) CONTAINS (.+)$/', $value, $match)) {
							$sql_where .= " AND f_gedcom LIKE CONCAT('%', :{$attr}contains1, '%', :{$attr}contains2, '%', :{$attr}contains3, '%')";
							$sql_params[$attr . 'contains1'] = $match[1];
							$sql_params[$attr . 'contains2'] = $match[2];
							$sql_params[$attr . 'contains3'] = $match[3];
							// Don't unset this filter. This is just initial filtering
						}
					}
				}

				$this->list = array();
				$rows       = Database::prepare(
					$sql_select . $sql_join . $sql_where . $sql_order_by
				)->execute($sql_params)->fetchAll();

				foreach ($rows as $row) {
					$this->list[] = Family::getInstance($row->xref, $WT_TREE, $row->gedcom);
				}
				break;

			default:
				throw new \DomainException('Invalid list name: ' . $listname);
		}

		$filters  = array();
		$filters2 = array();
		if (isset($attrs['filter1']) && count($this->list) > 0) {
			foreach ($attrs as $key => $value) {
				if (preg_match("/filter(\d)/", $key)) {
					$condition = $value;
					if (preg_match("/@(\w+)/", $condition, $match)) {
						$id    = $match[1];
						$value = "''";
						if ($id == "ID") {
							if (preg_match("/0 @(.+)@/", $this->gedrec, $match)) {
								$value = "'" . $match[1] . "'";
							}
						} elseif ($id == "fact") {
							$value = "'" . $this->fact . "'";
						} elseif ($id == "desc") {
							$value = "'" . $this->desc . "'";
						} else {
							if (preg_match("/\d $id (.+)/", $this->gedrec, $match)) {
								$value = "'" . str_replace("@", "", trim($match[1])) . "'";
							}
						}
						$condition = preg_replace("/@$id/", $value, $condition);
					}
					//-- handle regular expressions
					if (preg_match("/([A-Z:]+)\s*([^\s]+)\s*(.+)/", $condition, $match)) {
						$tag  = trim($match[1]);
						$expr = trim($match[2]);
						$val  = trim($match[3]);
						if (preg_match("/\\$(\w+)/", $val, $match)) {
							$val = $this->vars[$match[1]]['id'];
							$val = trim($val);
						}
						if ($val) {
							$searchstr = "";
							$tags      = explode(":", $tag);
							//-- only limit to a level number if we are specifically looking at a level
							if (count($tags) > 1) {
								$level = 1;
								foreach ($tags as $t) {
									if (!empty($searchstr)) {
										$searchstr .= "[^\n]*(\n[2-9][^\n]*)*\n";
									}
									//-- search for both EMAIL and _EMAIL... silly double gedcom standard
									if ($t == "EMAIL" || $t == "_EMAIL") {
										$t = "_?EMAIL";
									}
									$searchstr .= $level . " " . $t;
									$level++;
								}
							} else {
								if ($tag == "EMAIL" || $tag == "_EMAIL") {
									$tag = "_?EMAIL";
								}
								$t         = $tag;
								$searchstr = "1 " . $tag;
							}
							switch ($expr) {
								case "CONTAINS":
									if ($t == "PLAC") {
										$searchstr .= "[^\n]*[, ]*" . $val;
									} else {
										$searchstr .= "[^\n]*" . $val;
									}
									$filters[] = $searchstr;
									break;
								default:
									$filters2[] = array("tag" => $tag, "expr" => $expr, "val" => $val);
									break;
							}
						}
					}
				}
			}
		}
		//-- apply other filters to the list that could not be added to the search string
		if ($filters) {
			foreach ($this->list as $key => $record) {
				foreach ($filters as $filter) {
					if (!preg_match("/" . $filter . "/i", $record->privatizeGedcom(Auth::accessLevel($WT_TREE)))) {
						unset($this->list[$key]);
						break;
					}
				}
			}
		}
		if ($filters2) {
			$mylist = array();
			foreach ($this->list as $indi) {
				$key  = $indi->getXref();
				$grec = $indi->privatizeGedcom(Auth::accessLevel($WT_TREE));
				$keep = true;
				foreach ($filters2 as $filter) {
					if ($keep) {
						$tag  = $filter['tag'];
						$expr = $filter['expr'];
						$val  = $filter['val'];
						if ($val == "''") {
							$val = "";
						}
						$tags = explode(":", $tag);
						$t    = end($tags);
						$v    = $this->getGedcomValue($tag, 1, $grec);
						//-- check for EMAIL and _EMAIL (silly double gedcom standard :P)
						if ($t == "EMAIL" && empty($v)) {
							$tag  = str_replace("EMAIL", "_EMAIL", $tag);
							$tags = explode(":", $tag);
							$t    = end($tags);
							$v    = Functions::getSubRecord(1, $tag, $grec);
						}

						switch ($expr) {
							case "GTE":
								if ($t == "DATE") {
									$date1 = new Date($v);
									$date2 = new Date($val);
									$keep  = (Date::compare($date1, $date2) >= 0);
								} elseif ($val >= $v) {
									$keep = true;
								}
								break;
							case "LTE":
								if ($t == "DATE") {
									$date1 = new Date($v);
									$date2 = new Date($val);
									$keep  = (Date::compare($date1, $date2) <= 0);
								} elseif ($val >= $v) {
									$keep = true;
								}
								break;
							default:
								if ($v == $val) {
									$keep = true;
								} else {
									$keep = false;
								}
								break;
						}
					}
				}
				if ($keep) {
					$mylist[$key] = $indi;
				}
			}
			$this->list = $mylist;
		}

		switch ($sortby) {
			case 'NAME':
				uasort($this->list, '\Fisharebest\Webtrees\GedcomRecord::compare');
				break;
			case 'CHAN':
				uasort($this->list, function (GedcomRecord $x, GedcomRecord $y) {
					return $y->lastChangeTimestamp(true) - $x->lastChangeTimestamp(true);
				});
				break;
			case 'BIRT:DATE':
				uasort($this->list, '\Fisharebest\Webtrees\Individual::compareBirthDate');
				break;
			case 'DEAT:DATE':
				uasort($this->list, '\Fisharebest\Webtrees\Individual::compareDeathDate');
				break;
			case 'MARR:DATE':
				uasort($this->list, '\Fisharebest\Webtrees\Family::compareMarrDate');
				break;
			default:
				// unsorted or already sorted by SQL
				break;
		}

		array_push($this->repeats_stack, array($this->repeats, $this->repeat_bytes));
		$this->repeat_bytes = xml_get_current_line_number($this->parser) + 1;
	}

	/**
	 * XML <List>
	 */
	private function listEndHandler() {
		global $report;

		$this->process_repeats--;
		if ($this->process_repeats > 0) {
			return;
		}

		// Check if there is any list
		if (count($this->list) > 0) {
			$lineoffset = 0;
			foreach ($this->repeats_stack as $rep) {
				$lineoffset += $rep[1];
			}
			//-- read the xml from the file
			$lines = file($report);
			while ((strpos($lines[$lineoffset + $this->repeat_bytes], "<List") === false) && (($lineoffset + $this->repeat_bytes) > 0)) {
				$lineoffset--;
			}
			$lineoffset++;
			$reportxml = "<tempdoc>\n";
			$line_nr   = $lineoffset + $this->repeat_bytes;
			// List Level counter
			$count = 1;
			while (0 < $count) {
				if (strpos($lines[$line_nr], "<List") !== false) {
					$count++;
				} elseif (strpos($lines[$line_nr], "</List") !== false) {
					$count--;
				}
				if (0 < $count) {
					$reportxml .= $lines[$line_nr];
				}
				$line_nr++;
			}
			// No need to drag this
			unset($lines);
			$reportxml .= "</tempdoc>";
			// Save original values
			array_push($this->parser_stack, $this->parser);
			$oldgedrec = $this->gedrec;

			$this->list_total   = count($this->list);
			$this->list_private = 0;
			foreach ($this->list as $record) {
				if ($record->canShow()) {
					$this->gedrec = $record->privatizeGedcom(Auth::accessLevel($record->getTree()));
					//-- start the sax parser
					$repeat_parser = xml_parser_create();
					$this->parser  = $repeat_parser;
					xml_parser_set_option($repeat_parser, XML_OPTION_CASE_FOLDING, false);
					xml_set_element_handler($repeat_parser, array($this, 'startElement'), array($this, 'endElement'));
					xml_set_character_data_handler($repeat_parser, array($this, 'characterData'));
					if (!xml_parse($repeat_parser, $reportxml, true)) {
						throw new \DomainException(sprintf(
							'ListEHandler XML error: %s at line %d',
							xml_error_string(xml_get_error_code($repeat_parser)),
							xml_get_current_line_number($repeat_parser)
						));
					}
					xml_parser_free($repeat_parser);
				} else {
					$this->list_private++;
				}
			}
			$this->list   = array();
			$this->parser = array_pop($this->parser_stack);
			$this->gedrec = $oldgedrec;
		}
		list($this->repeats, $this->repeat_bytes) = array_pop($this->repeats_stack);
	}

	/**
	 * XML <ListTotal> element handler
	 *
	 * Prints the total number of records in a list
	 * The total number is collected from
	 * List and Relatives
	 */
	private function listTotalStartHandler() {
		if ($this->list_private == 0) {
			$this->current_element->addText($this->list_total);
		} else {
			$this->current_element->addText(($this->list_total - $this->list_private) . " / " . $this->list_total);
		}
	}

	/**
	 * XML <Relatives>
	 *
	 * @param array $attrs an array of key value pairs for the attributes
	 */
	private function relativesStartHandler($attrs) {
		global $WT_TREE;

		$this->process_repeats++;
		if ($this->process_repeats > 1) {
			return;
		}

		$sortby = "NAME";
		if (isset($attrs['sortby'])) {
			$sortby = $attrs['sortby'];
		}
		$match = array();
		if (preg_match("/\\$(\w+)/", $sortby, $match)) {
			$sortby = $this->vars[$match[1]]['id'];
			$sortby = trim($sortby);
		}

		$maxgen = -1;
		if (isset($attrs['maxgen'])) {
			$maxgen = $attrs['maxgen'];
		}
		if ($maxgen == "*") {
			$maxgen = -1;
		}

		$group = "child-family";
		if (isset($attrs['group'])) {
			$group = $attrs['group'];
		}
		if (preg_match("/\\$(\w+)/", $group, $match)) {
			$group = $this->vars[$match[1]]['id'];
			$group = trim($group);
		}

		$id = "";
		if (isset($attrs['id'])) {
			$id = $attrs['id'];
		}
		if (preg_match("/\\$(\w+)/", $id, $match)) {
			$id = $this->vars[$match[1]]['id'];
			$id = trim($id);
		}

		$this->list = array();
		$person     = Individual::getInstance($id, $WT_TREE);
		if (!empty($person)) {
			$this->list[$id] = $person;
			switch ($group) {
				case "child-family":
					foreach ($person->getChildFamilies() as $family) {
						$husband = $family->getHusband();
						$wife    = $family->getWife();
						if (!empty($husband)) {
							$this->list[$husband->getXref()] = $husband;
						}
						if (!empty($wife)) {
							$this->list[$wife->getXref()] = $wife;
						}
						$children = $family->getChildren();
						foreach ($children as $child) {
							if (!empty($child)) {
								$this->list[$child->getXref()] = $child;
							}
						}
					}
					break;
				case "spouse-family":
					foreach ($person->getSpouseFamilies() as $family) {
						$husband = $family->getHusband();
						$wife    = $family->getWife();
						if (!empty($husband)) {
							$this->list[$husband->getXref()] = $husband;
						}
						if (!empty($wife)) {
							$this->list[$wife->getXref()] = $wife;
						}
						$children = $family->getChildren();
						foreach ($children as $child) {
							if (!empty($child)) {
								$this->list[$child->getXref()] = $child;
							}
						}
					}
					break;
				case "direct-ancestors":
					$this->addAncestors($this->list, $id, false, $maxgen);
					break;
				case "ancestors":
					$this->addAncestors($this->list, $id, true, $maxgen);
					break;
				case "descendants":
					$this->list[$id]->generation = 1;
					$this->addDescendancy($this->list, $id, false, $maxgen);
					break;
				case "all":
					$this->addAncestors($this->list, $id, true, $maxgen);
					$this->addDescendancy($this->list, $id, true, $maxgen);
					break;
			}
		}

		switch ($sortby) {
			case 'NAME':
				uasort($this->list, '\Fisharebest\Webtrees\GedcomRecord::compare');
				break;
			case 'BIRT:DATE':
				uasort($this->list, '\Fisharebest\Webtrees\Individual::compareBirthDate');
				break;
			case 'DEAT:DATE':
				uasort($this->list, '\Fisharebest\Webtrees\Individual::compareDeathDate');
				break;
			case 'generation':
				$newarray = array();
				reset($this->list);
				$genCounter = 1;
				while (count($newarray) < count($this->list)) {
					foreach ($this->list as $key => $value) {
						$this->generation = $value->generation;
						if ($this->generation == $genCounter) {
							$newarray[$key]             = new \stdClass;
							$newarray[$key]->generation = $this->generation;
						}
					}
					$genCounter++;
				}
				$this->list = $newarray;
				break;
			default:
				// unsorted
				break;
		}
		array_push($this->repeats_stack, array($this->repeats, $this->repeat_bytes));
		$this->repeat_bytes = xml_get_current_line_number($this->parser) + 1;
	}

	/**
	 * XML </ Relatives>
	 */
	private function relativesEndHandler() {
		global $report, $WT_TREE;

		$this->process_repeats--;
		if ($this->process_repeats > 0) {
			return;
		}

		// Check if there is any relatives
		if (count($this->list) > 0) {
			$lineoffset = 0;
			foreach ($this->repeats_stack as $rep) {
				$lineoffset += $rep[1];
			}
			//-- read the xml from the file
			$lines = file($report);
			while ((strpos($lines[$lineoffset + $this->repeat_bytes], "<Relatives") === false) && (($lineoffset + $this->repeat_bytes) > 0)) {
				$lineoffset--;
			}
			$lineoffset++;
			$reportxml = "<tempdoc>\n";
			$line_nr   = $lineoffset + $this->repeat_bytes;
			// Relatives Level counter
			$count = 1;
			while (0 < $count) {
				if (strpos($lines[$line_nr], "<Relatives") !== false) {
					$count++;
				} elseif (strpos($lines[$line_nr], "</Relatives") !== false) {
					$count--;
				}
				if (0 < $count) {
					$reportxml .= $lines[$line_nr];
				}
				$line_nr++;
			}
			// No need to drag this
			unset($lines);
			$reportxml .= "</tempdoc>\n";
			// Save original values
			array_push($this->parser_stack, $this->parser);
			$oldgedrec = $this->gedrec;

			$this->list_total   = count($this->list);
			$this->list_private = 0;
			foreach ($this->list as $key => $value) {
				if (isset($value->generation)) {
					$this->generation = $value->generation;
				}
				$tmp          = GedcomRecord::getInstance($key, $WT_TREE);
				$this->gedrec = $tmp->privatizeGedcom(Auth::accessLevel($WT_TREE));

				$repeat_parser = xml_parser_create();
				$this->parser  = $repeat_parser;
				xml_parser_set_option($repeat_parser, XML_OPTION_CASE_FOLDING, false);
				xml_set_element_handler($repeat_parser, array($this, 'startElement'), array($this, 'endElement'));
				xml_set_character_data_handler($repeat_parser, array($this, 'characterData'));

				if (!xml_parse($repeat_parser, $reportxml, true)) {
					throw new \DomainException(sprintf("RelativesEHandler XML error: %s at line %d", xml_error_string(xml_get_error_code($repeat_parser)), xml_get_current_line_number($repeat_parser)));
				}
				xml_parser_free($repeat_parser);
			}
			// Clean up the list array
			$this->list   = array();
			$this->parser = array_pop($this->parser_stack);
			$this->gedrec = $oldgedrec;
		}
		list($this->repeats, $this->repeat_bytes) = array_pop($this->repeats_stack);
	}

	/**
	 * XML <Generation /> element handler
	 *
	 * Prints the number of generations
	 */
	private function generationStartHandler() {
		$this->current_element->addText($this->generation);
	}

	/**
	 * XML <NewPage /> element handler
	 *
	 * Has to be placed in an element (header, pageheader, body or footer)
	 */
	private function newPageStartHandler() {
		$temp = "addpage";
		$this->wt_report->addElement($temp);
	}

	/**
	 * XML <html>
	 *
	 * @param string  $tag   HTML tag name
	 * @param array[] $attrs an array of key value pairs for the attributes
	 */
	private function htmlStartHandler($tag, $attrs) {
		if ($tag === "tempdoc") {
			return;
		}
		array_push($this->wt_report_stack, $this->wt_report);
		$this->wt_report       = $this->report_root->createHTML($tag, $attrs);
		$this->current_element = $this->wt_report;

		array_push($this->print_data_stack, $this->print_data);
		$this->print_data = true;
	}

	/**
	 * XML </html>
	 *
	 * @param string $tag
	 */
	private function htmlEndHandler($tag) {
		if ($tag === "tempdoc") {
			return;
		}

		$this->print_data      = array_pop($this->print_data_stack);
		$this->current_element = $this->wt_report;
		$this->wt_report       = array_pop($this->wt_report_stack);
		if (!is_null($this->wt_report)) {
			$this->wt_report->addElement($this->current_element);
		} else {
			$this->wt_report = $this->current_element;
		}
	}

	/**
	 * Handle <Input>
	 */
	private function inputStartHandler() {
		// Dummy function, to prevent the default HtmlStartHandler() being called
	}

	/**
	 * Handle </Input>
	 */
	private function inputEndHandler() {
		// Dummy function, to prevent the default HtmlEndHandler() being called
	}

	/**
	 * Handle <Report>
	 */
	private function reportStartHandler() {
		// Dummy function, to prevent the default HtmlStartHandler() being called
	}

	/**
	 * Handle </Report>
	 */
	private function reportEndHandler() {
		// Dummy function, to prevent the default HtmlEndHandler() being called
	}

	/**
	 * XML </titleEndHandler>
	 */
	private function titleEndHandler() {
		$this->report_root->addTitle($this->text);
	}

	/**
	 * XML </descriptionEndHandler>
	 */
	private function descriptionEndHandler() {
		$this->report_root->addDescription($this->text);
	}

	/**
	 * Create a list of all descendants.
	 *
	 * @param string[] $list
	 * @param string   $pid
	 * @param bool  $parents
	 * @param int  $generations
	 */
	private function addDescendancy(&$list, $pid, $parents = false, $generations = -1) {
		global $WT_TREE;

		$person = Individual::getInstance($pid, $WT_TREE);
		if ($person === null) {
			return;
		}
		if (!isset($list[$pid])) {
			$list[$pid] = $person;
		}
		if (!isset($list[$pid]->generation)) {
			$list[$pid]->generation = 0;
		}
		foreach ($person->getSpouseFamilies() as $family) {
			if ($parents) {
				$husband = $family->getHusband();
				$wife    = $family->getWife();
				if ($husband) {
					$list[$husband->getXref()] = $husband;
					if (isset($list[$pid]->generation)) {
						$list[$husband->getXref()]->generation = $list[$pid]->generation - 1;
					} else {
						$list[$husband->getXref()]->generation = 1;
					}
				}
				if ($wife) {
					$list[$wife->getXref()] = $wife;
					if (isset($list[$pid]->generation)) {
						$list[$wife->getXref()]->generation = $list[$pid]->generation - 1;
					} else {
						$list[$wife->getXref()]->generation = 1;
					}
				}
			}
			$children = $family->getChildren();
			foreach ($children as $child) {
				if ($child) {
					$list[$child->getXref()] = $child;
					if (isset($list[$pid]->generation)) {
						$list[$child->getXref()]->generation = $list[$pid]->generation + 1;
					} else {
						$list[$child->getXref()]->generation = 2;
					}
				}
			}
			if ($generations == -1 || $list[$pid]->generation + 1 < $generations) {
				foreach ($children as $child) {
					$this->addDescendancy($list, $child->getXref(), $parents, $generations); // recurse on the childs family
				}
			}
		}
	}

	/**
	 * Create a list of all ancestors.
	 *
	 * @param string[] $list
	 * @param string   $pid
	 * @param bool  $children
	 * @param int  $generations
	 */
	private function addAncestors(&$list, $pid, $children = false, $generations = -1) {
		global $WT_TREE;

		$genlist                = array($pid);
		$list[$pid]->generation = 1;
		while (count($genlist) > 0) {
			$id = array_shift($genlist);
			if (strpos($id, 'empty') === 0) {
				continue; // id can be something like “empty7”
			}
			$person = Individual::getInstance($id, $WT_TREE);
			foreach ($person->getChildFamilies() as $family) {
				$husband = $family->getHusband();
				$wife    = $family->getWife();
				if ($husband) {
					$list[$husband->getXref()]             = $husband;
					$list[$husband->getXref()]->generation = $list[$id]->generation + 1;
				}
				if ($wife) {
					$list[$wife->getXref()]             = $wife;
					$list[$wife->getXref()]->generation = $list[$id]->generation + 1;
				}
				if ($generations == -1 || $list[$id]->generation + 1 < $generations) {
					if ($husband) {
						array_push($genlist, $husband->getXref());
					}
					if ($wife) {
						array_push($genlist, $wife->getXref());
					}
				}
				if ($children) {
					foreach ($family->getChildren() as $child) {
						$list[$child->getXref()] = $child;
						if (isset($list[$id]->generation)) {
							$list[$child->getXref()]->generation = $list[$id]->generation;
						} else {
							$list[$child->getXref()]->generation = 1;
						}
					}
				}
			}
		}
	}

	/**
	 * get gedcom tag value
	 *
	 * @param string  $tag    The tag to find, use : to delineate subtags
	 * @param int $level  The gedcom line level of the first tag to find, setting level to 0 will cause it to use 1+ the level of the incoming record
	 * @param string  $gedrec The gedcom record to get the value from
	 *
	 * @return string the value of a gedcom tag from the given gedcom record
	 */
	private function getGedcomValue($tag, $level, $gedrec) {
		global $WT_TREE;

		if (empty($gedrec)) {
			return '';
		}
		$tags      = explode(':', $tag);
		$origlevel = $level;
		if ($level == 0) {
			$level = $gedrec{0} + 1;
		}

		$subrec = $gedrec;
		foreach ($tags as $t) {
			$lastsubrec = $subrec;
			$subrec     = Functions::getSubRecord($level, "$level $t", $subrec);
			if (empty($subrec) && $origlevel == 0) {
				$level--;
				$subrec = Functions::getSubRecord($level, "$level $t", $lastsubrec);
			}
			if (empty($subrec)) {
				if ($t == "TITL") {
					$subrec = Functions::getSubRecord($level, "$level ABBR", $lastsubrec);
					if (!empty($subrec)) {
						$t = "ABBR";
					}
				}
				if (empty($subrec)) {
					if ($level > 0) {
						$level--;
					}
					$subrec = Functions::getSubRecord($level, "@ $t", $gedrec);
					if (empty($subrec)) {
						return '';
					}
				}
			}
			$level++;
		}
		$level--;
		$ct = preg_match("/$level $t(.*)/", $subrec, $match);
		if ($ct == 0) {
			$ct = preg_match("/$level @.+@ (.+)/", $subrec, $match);
		}
		if ($ct == 0) {
			$ct = preg_match("/@ $t (.+)/", $subrec, $match);
		}
		if ($ct > 0) {
			$value = trim($match[1]);
			if ($t == 'NOTE' && preg_match('/^@(.+)@$/', $value, $match)) {
				$note = Note::getInstance($match[1], $WT_TREE);
				if ($note) {
					$value = $note->getNote();
				} else {
					//-- set the value to the id without the @
					$value = $match[1];
				}
			}
			if ($level != 0 || $t != "NOTE") {
				$value .= Functions::getCont($level + 1, $subrec);
			}

			return $value;
		}

		return "";
	}

	/**
	 * Replace variable identifiers with their values.
	 *
	 * @param string $expression An expression such as "$foo == 123"
	 * @param bool   $quote      Whether to add quotation marks
	 *
	 * @return string
	 */
	private function substituteVars($expression, $quote) {
		$that = $this; // PHP5.3 cannot access $this inside a closure
		return preg_replace_callback(
			'/\$(\w+)/',
			function ($matches) use ($that, $quote) {
				if (isset($that->vars[$matches[1]]['id'])) {
					if ($quote) {
						return "'" . addcslashes($that->vars[$matches[1]]['id'], "'") . "'";
					} else {
						return $that->vars[$matches[1]]['id'];
					}
				} else {
					Log::addErrorLog(sprintf('Undefined variable $%s in report', $matches[1]));

					return '$' . $matches[1];
				}
			},
			$expression
		);
	}
}
