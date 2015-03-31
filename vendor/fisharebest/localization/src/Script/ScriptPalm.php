<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPalm - Representation of the Palmyrene script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPalm extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Palm';
	}

	/** {@inheritdoc} */
	public function number() {
		return '126';
	}
}
