<?php namespace Fisharebest\Localization;

/**
 * Class ScriptBrah - Representation of the Brahmi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBrah extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Brah';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '𑁦',
			'1' => '𑁧',
			'2' => '𑁨',
			'3' => '𑁩',
			'4' => '𑁪',
			'5' => '𑁫',
			'6' => '𑁬',
			'7' => '𑁭',
			'8' => '𑁮',
			'9' => '𑁯',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '300';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Brahmi';
	}
}
