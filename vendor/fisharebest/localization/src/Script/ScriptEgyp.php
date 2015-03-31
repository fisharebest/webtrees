<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptEgyp - Representation of the Egyptian hieroglyphs script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptEgyp extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Egyp';
	}

	/** {@inheritdoc} */
	public function number() {
		return '050';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Egyptian_Hieroglyphs';
	}
}
