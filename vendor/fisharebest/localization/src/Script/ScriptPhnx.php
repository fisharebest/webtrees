<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPhnx - Representation of the Phoenician script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPhnx extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Phnx';
	}

	/** {@inheritdoc} */
	public function number() {
		return '115';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Phoenician';
	}
}
