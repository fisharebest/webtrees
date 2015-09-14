<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHani - Representation of the Han (Hanzi, Kanji, Hanja) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHani extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Hani';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('〇', '一', '二', '三', '四', '五', '六', '七', '八', '九');
	}

	/** {@inheritdoc} */
	public function number() {
		return '500';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Han';
	}
}
