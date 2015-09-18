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
		return array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
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
