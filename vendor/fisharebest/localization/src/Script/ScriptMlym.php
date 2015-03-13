<?php namespace Fisharebest\Localization;

/**
 * Class ScriptMlym - Representation of the Malayalam script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMlym extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Mlym';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '൦',
			'1' => '൧',
			'2' => '൨',
			'3' => '൩',
			'4' => '൪',
			'5' => '൫',
			'6' => '൬',
			'7' => '൭',
			'8' => '൮',
			'9' => '൯',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '347';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Malayalam';
	}
}
