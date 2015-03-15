<?php namespace Fisharebest\Localization;

/**
 * Class ScriptKnda - Representation of the Kannada script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKnda extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Knda';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '೦',
			'1' => '೧',
			'2' => '೨',
			'3' => '೩',
			'4' => '೪',
			'5' => '೫',
			'6' => '೬',
			'7' => '೭',
			'8' => '೮',
			'9' => '೯',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '345';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Kannada';
	}
}
