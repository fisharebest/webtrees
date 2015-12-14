<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHang - Representation of the Hangul script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHang extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Hang';
	}

	public function number() {
		return '286';
	}

	public function unicodeName() {
		return 'Hangul';
	}
}
