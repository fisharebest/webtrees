<?php namespace Fisharebest\Localization;

/**
 * Class ScriptTelu - Representation of the Telugu script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTelu extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Telu';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '౦',
			'1' => '౧',
			'2' => '౨',
			'3' => '౩',
			'4' => '౪',
			'5' => '౫',
			'6' => '౬',
			'7' => '౭',
			'8' => '౮',
			'9' => '౯',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '340';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Telugu';
	}
}
