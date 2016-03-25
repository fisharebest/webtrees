<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKana - Representation of the Katakana script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKana extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Kana';
	}

	public function number() {
		return '411';
	}

	public function unicodeName() {
		return 'Katakana';
	}
}
