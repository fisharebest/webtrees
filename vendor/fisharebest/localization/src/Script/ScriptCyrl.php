<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCyrl - Representation of the Cyrillic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptCyrl extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Cyrl';
	}

	public function number() {
		return '220';
	}

	public function unicodeName() {
		return 'Cyrillic';
	}
}
