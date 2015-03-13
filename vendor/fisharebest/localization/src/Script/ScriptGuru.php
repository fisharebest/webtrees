<?php namespace Fisharebest\Localization;

/**
 * Class ScriptGuru - Representation of the Gurmukhi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGuru extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Guru';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '੦',
			'1' => '੧',
			'2' => '੨',
			'3' => '੩',
			'4' => '੪',
			'5' => '੫',
			'6' => '੬',
			'7' => '੭',
			'8' => '੮',
			'9' => '੯',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '310';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Gurmukhi';
	}
}
