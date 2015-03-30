<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHluw - Representation of the Anatolian Hieroglyphs (Luwian Hieroglyphs, Hittite Hieroglyphs) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHluw extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Hluw';
	}

	/** {@inheritdoc} */
	public function number() {
		return '080';
	}
}
