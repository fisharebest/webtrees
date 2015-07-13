<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptAdlm - Representation of the Adlam script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptAdlm extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Adlm';
	}

	/** {@inheritdoc} */
	public function number() {
		return '166';
	}
}
