<?php namespace Fisharebest\Localization;

/**
 * Class ScriptKali - Representation of the Kayah Li script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKali extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Kali';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '꤀',
			'1' => '꤁',
			'2' => '꤂',
			'3' => '꤃',
			'4' => '꤄',
			'5' => '꤅',
			'6' => '꤆',
			'7' => '꤇',
			'8' => '꤈',
			'9' => '꤉',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '357';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Kayah_Li';
	}
}
