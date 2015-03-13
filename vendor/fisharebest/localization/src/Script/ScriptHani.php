<?php namespace Fisharebest\Localization;

/**
 * Class ScriptHani - Representation of the Han (Hanzi, Kanji, Hanja) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHani extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Hani';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '〇',
			'1' => '一',
			'2' => '二',
			'3' => '三',
			'4' => '四',
			'5' => '五',
			'6' => '六',
			'7' => '七',
			'8' => '八',
			'9' => '九',
		);
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
