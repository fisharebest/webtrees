<?php namespace Fisharebest\Localization;

/**
 * Class ScriptBeng - Representation of the Bengali script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBeng extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Beng';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '০',
			'1' => '১',
			'2' => '২',
			'3' => '৩',
			'4' => '৪',
			'5' => '৫',
			'6' => '৬',
			'7' => '৭',
			'8' => '৮',
			'9' => '৯',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '325';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Bengali';
	}
}
