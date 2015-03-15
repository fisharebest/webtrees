<?php namespace Fisharebest\Localization;

/**
 * Class ScriptPhnx - Representation of the Phoenician script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPhnx extends Script {
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
