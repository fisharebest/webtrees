<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSinh - Representation of the Sinhala script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSinh extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Sinh';
	}

	public function number() {
		return '348';
	}

	public function unicodeName() {
		return 'Sinhala';
	}
}
