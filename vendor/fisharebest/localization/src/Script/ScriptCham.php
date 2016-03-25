<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCham - Representation of the Cham script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptCham extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Cham';
	}

	public function numerals() {
		return array('꩐', '꩑', '꩒', '꩓', '꩔', '꩕', '꩖', '꩗', '꩘', '꩙');
	}

	public function number() {
		return '358';
	}

	public function unicodeName() {
		return 'Cham';
	}
}
