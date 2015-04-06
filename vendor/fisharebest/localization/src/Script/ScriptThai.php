<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptThai - Representation of the Thai script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptThai extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Thai';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '๐',
			'1' => '๑',
			'2' => '๒',
			'3' => '๓',
			'4' => '๔',
			'5' => '๕',
			'6' => '๖',
			'7' => '๗',
			'8' => '๘',
			'9' => '๙',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '352';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Thai';
	}
}
