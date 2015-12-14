<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHebr - Representation of the Hebrew script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHebr extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Hebr';
	}

	public function number() {
		return '125';
	}

	public function unicodeName() {
		return 'Hebrew';
	}
}
