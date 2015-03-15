<?php namespace Fisharebest\Localization;

/**
 * Class ScriptOgam - Representation of the Ogham script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptOgam extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Ogam';
	}

	/** {@inheritdoc} */
	public function number() {
		return '212';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Ogham';
	}
}
