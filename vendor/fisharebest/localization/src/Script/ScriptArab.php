<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptArab - Representation of the Arabic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptArab extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Arab';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '٠',
			'1' => '١',
			'2' => '٢',
			'3' => '٣',
			'4' => '٤',
			'5' => '٥',
			'6' => '٦',
			'7' => '٧',
			'8' => '٨',
			'9' => '٩',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '160';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Arabic';
	}
}
