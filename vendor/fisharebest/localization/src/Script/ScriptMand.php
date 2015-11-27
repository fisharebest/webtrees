<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMand - Representation of the Mandaic, Mandaean script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMand extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Mand';
	}

	public function number() {
		return '140';
	}

	public function unicodeName() {
		return 'Mandaic';
	}
}
