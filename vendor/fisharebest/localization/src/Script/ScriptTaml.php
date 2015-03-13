<?php namespace Fisharebest\Localization;

/**
 * Class ScriptTaml - Representation of the Tamil script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTaml extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Taml';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '௦',
			'1' => '௧',
			'2' => '௨',
			'3' => '௩',
			'4' => '௪',
			'5' => '௫',
			'6' => '௬',
			'7' => '௭',
			'8' => '௮',
			'9' => '௯',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '346';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Tamil';
	}
}
