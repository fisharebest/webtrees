<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTeng - Representation of the Tengwar script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTeng extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Teng';
	}

	/** {@inheritdoc} */
	public function number() {
		return '290';
	}
}
