<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBlis - Representation of the Blissymbols script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBlis extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Blis';
	}

	/** {@inheritdoc} */
	public function number() {
		return '550';
	}
}
