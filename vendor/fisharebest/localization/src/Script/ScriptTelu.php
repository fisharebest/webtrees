<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTelu - Representation of the Telugu script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTelu extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Telu';
	}

	public function numerals() {
		return array('౦', '౧', '౨', '౩', '౪', '౫', '౬', '౭', '౮', '౯');
	}

	public function number() {
		return '340';
	}

	public function unicodeName() {
		return 'Telugu';
	}
}
