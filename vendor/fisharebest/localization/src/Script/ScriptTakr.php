<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTakr - Representation of the Takri, Ṭākrī, Ṭāṅkrī script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTakr extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Takr';
	}

	public function numerals() {
		return array('𑛀', '𑛁', '𑛂', '𑛃', '𑛄', '𑛅', '𑛆', '𑛇', '𑛈', '𑛉');
	}

	public function number() {
		return '321';
	}

	public function unicodeName() {
		return 'Takri';
	}
}
