<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptArab - Representation of the Arabic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptArab extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Arab';
	}

	public function numerals() {
		return array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
	}

	public function number() {
		return '160';
	}

	public function unicodeName() {
		return 'Arabic';
	}
}
