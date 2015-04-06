<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSyrn - Representation of the Syriac (Eastern variant) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSyrn extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Syrn';
	}

	/** {@inheritdoc} */
	public function number() {
		return '136';
	}
}
