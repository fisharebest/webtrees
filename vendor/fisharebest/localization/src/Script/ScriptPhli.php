<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPhli - Representation of the Inscriptional Pahlavi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPhli extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Phli';
	}

	public function number() {
		return '131';
	}

	public function unicodeName() {
		return 'Inscriptional_Pahlavi';
	}
}
