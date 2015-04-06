<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMero - Representation of the Meroitic Hieroglyphs script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMero extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Mero';
	}

	/** {@inheritdoc} */
	public function number() {
		return '100';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Meroitic_Hieroglyphs';
	}
}
