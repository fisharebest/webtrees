<?php namespace Fisharebest\Localization;

/**
 * Class ScriptBali - Representation of the Balinese script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBali extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Bali';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '᭐',
			'1' => '᭑',
			'2' => '᭒',
			'3' => '᭓',
			'4' => '᭔',
			'5' => '᭕',
			'6' => '᭖',
			'7' => '᭗',
			'8' => '᭘',
			'9' => '᭙',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '360';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Balinese';
	}
}
