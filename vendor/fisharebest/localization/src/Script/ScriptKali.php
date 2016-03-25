<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKali - Representation of the Kayah Li script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKali extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Kali';
	}

	public function numerals() {
		return array('꤀', '꤁', '꤂', '꤃', '꤄', '꤅', '꤆', '꤇', '꤈', '꤉');
	}

	public function number() {
		return '357';
	}

	public function unicodeName() {
		return 'Kayah_Li';
	}
}
