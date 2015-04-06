<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKthi - Representation of the Kaithi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKthi extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Kthi';
	}

	/** {@inheritdoc} */
	public function number() {
		return '317';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Kaithi';
	}
}
