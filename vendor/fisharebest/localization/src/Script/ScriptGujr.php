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
		return array('૦', '૧', '૨', '૩', '૪', '૫', '૬', '૭', '૮', '૯');
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
