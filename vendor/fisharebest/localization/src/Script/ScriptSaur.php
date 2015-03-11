<?php namespace Fisharebest\Localization;

/**
 * Class ScriptSaur - Representation of the Saurashtra script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSaur extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Saur';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '꣐',
			'1' => '꣑',
			'2' => '꣒',
			'3' => '꣓',
			'4' => '꣔',
			'5' => '꣕',
			'6' => '꣖',
			'7' => '꣗',
			'8' => '꣘',
			'9' => '꣙',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '344';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Saurashtra';
	}
}
