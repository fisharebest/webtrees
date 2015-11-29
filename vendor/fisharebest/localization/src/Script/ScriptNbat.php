<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNbat - Representation of the Nabataean script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptNbat extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Nbat';
	}

	public function number() {
		return '159';
	}

	public function unicodeName() {
		return 'Nabataean';
	}
}
