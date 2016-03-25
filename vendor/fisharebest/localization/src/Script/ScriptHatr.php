<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHatr - Representation of the Hatran script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHatr extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Hatr';
	}

	public function number() {
		return '127';
	}

	public function unicodeName() {
		return 'Hatran';
	}
}
