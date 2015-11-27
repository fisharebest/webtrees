<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptShrd - Representation of the Sharada, Śāradā script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptShrd extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Shrd';
	}

	public function numerals() {
		return array('𑇐', '𑇑', '𑇒', '𑇓', '𑇔', '𑇕', '𑇖', '𑇗', '𑇘', '𑇙');
	}

	public function number() {
		return '319';
	}

	public function unicodeName() {
		return 'Sharada';
	}
}
