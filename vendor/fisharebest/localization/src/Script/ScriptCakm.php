<?php namespace Fisharebest\Localization;

/**
 * Class ScriptCakm - Representation of the Chakma script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptCakm extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Cakm';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '𑄶',
			'1' => '𑄷',
			'2' => '𑄸',
			'3' => '𑄹',
			'4' => '𑄺',
			'5' => '𑄻',
			'6' => '𑄼',
			'7' => '𑄽',
			'8' => '𑄾',
			'9' => '𑄿',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '349';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Chakma';
	}
}
