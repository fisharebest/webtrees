<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptGujr - Representation of the Gujarati script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGujr extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Gujr';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '૦',
			'1' => '૧',
			'2' => '૨',
			'3' => '૩',
			'4' => '૪',
			'5' => '૫',
			'6' => '૬',
			'7' => '૭',
			'8' => '૮',
			'9' => '૯',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '320';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Gujarati';
	}
}
