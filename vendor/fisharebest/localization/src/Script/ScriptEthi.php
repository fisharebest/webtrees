<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptEthi - Representation of the Ethiopic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptEthi extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Ethi';
	}

	public function number() {
		return '430';
	}

	public function unicodeName() {
		return 'Ethiopic';
	}
}
