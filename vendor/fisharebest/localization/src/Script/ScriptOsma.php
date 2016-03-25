<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptOsma - Representation of the Osmanya script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptOsma extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Osma';
	}

	public function numerals() {
		return array('𐒠', '𐒡', '𐒢', '𐒣', '𐒤', '𐒥', '𐒦', '𐒧', '𐒨', '𐒩');
	}

	public function number() {
		return '260';
	}

	public function unicodeName() {
		return 'Osmanya';
	}
}
