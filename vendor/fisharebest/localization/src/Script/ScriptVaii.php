<?php namespace Fisharebest\Localization;

/**
 * Class ScriptVaii - Representation of the Vai script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptVaii extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Vaii';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '꘠',
			'1' => '꘡',
			'2' => '꘢',
			'3' => '꘣',
			'4' => '꘤',
			'5' => '꘥',
			'6' => '꘦',
			'7' => '꘧',
			'8' => '꘨',
			'9' => '꘩',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '470';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Vai';
	}
}
