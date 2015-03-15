<?php namespace Fisharebest\Localization;

/**
 * Class ScriptMerc - Representation of the Meroitic Cursive script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMerc extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Merc';
	}

	/** {@inheritdoc} */
	public function number() {
		return '101';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Meroitic_Cursive';
	}
}
