<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCari - Representation of the Carian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptCari extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Cari';
	}

	public function number() {
		return '201';
	}

	public function unicodeName() {
		return 'Carian';
	}
}
