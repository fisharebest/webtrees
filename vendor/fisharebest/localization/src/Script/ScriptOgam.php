<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptOgam - Representation of the Ogham script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptOgam extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Ogam';
	}

	public function number() {
		return '212';
	}

	public function unicodeName() {
		return 'Ogham';
	}
}
