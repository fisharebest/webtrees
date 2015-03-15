<?php namespace Fisharebest\Localization;

/**
 * Class ScriptPhli - Representation of the Inscriptional Pahlavi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPhli extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Phli';
	}

	/** {@inheritdoc} */
	public function number() {
		return '131';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Inscriptional_Pahlavi';
	}
}
