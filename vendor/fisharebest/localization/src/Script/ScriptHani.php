<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHani - Representation of the Han (Hanzi, Kanji, Hanja) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHani extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Hani';
	}

	public function numerals() {
		return array('〇', '一', '二', '三', '四', '五', '六', '七', '八', '九');
	}

	public function number() {
		return '500';
	}

	public function unicodeName() {
		return 'Han';
	}
}
