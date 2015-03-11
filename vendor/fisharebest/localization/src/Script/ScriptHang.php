<?php namespace Fisharebest\Localization;

/**
 * Class ScriptHang - Representation of the Hangul script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHang extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Hang';
	}

	/** {@inheritdoc} */
	public function number() {
		return '286';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Hangul';
	}
}
