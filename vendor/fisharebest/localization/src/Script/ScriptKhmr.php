<?php namespace Fisharebest\Localization;

/**
 * Class ScriptKhmr - Representation of the Khmer script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKhmr extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Khmr';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '០',
			'1' => '១',
			'2' => '២',
			'3' => '៣',
			'4' => '៤',
			'5' => '៥',
			'6' => '៦',
			'7' => '៧',
			'8' => '៨',
			'9' => '៩',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '355';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Khmer';
	}
}
