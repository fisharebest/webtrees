<?php namespace Fisharebest\Localization;

/**
 * Class ScriptOrya - Representation of the Oriya script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptOrya extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Orya';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '୦',
			'1' => '୧',
			'2' => '୨',
			'3' => '୩',
			'4' => '୪',
			'5' => '୫',
			'6' => '୬',
			'7' => '୭',
			'8' => '୮',
			'9' => '୯',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '327';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Oriya';
	}
}
