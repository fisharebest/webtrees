<?php namespace Fisharebest\Localization;

/**
 * Class ScriptOsma - Representation of the Osmanya script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptOsma extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Osma';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '𐒠',
			'1' => '𐒡',
			'2' => '𐒢',
			'3' => '𐒣',
			'4' => '𐒤',
			'5' => '𐒥',
			'6' => '𐒦',
			'7' => '𐒧',
			'8' => '𐒨',
			'9' => '𐒩',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '260';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Osmanya';
	}
}
