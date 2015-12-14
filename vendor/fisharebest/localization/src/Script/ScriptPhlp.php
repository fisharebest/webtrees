<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPhlp - Representation of the Psalter Pahlavi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPhlp extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Phlp';
	}

	public function number() {
		return '132';
	}

	public function unicodeName() {
		return 'Psalter_Pahlavi';
	}
}
