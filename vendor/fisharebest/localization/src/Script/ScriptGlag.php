<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptGlag - Representation of the Glagolitic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGlag extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Glag';
	}

	public function number() {
		return '225';
	}

	public function unicodeName() {
		return 'Glagolitic';
	}
}
