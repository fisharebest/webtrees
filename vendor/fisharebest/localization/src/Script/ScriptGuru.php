<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptGuru - Representation of the Gurmukhi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGuru extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Guru';
	}

	public function numerals() {
		return array('੦', '੧', '੨', '੩', '੪', '੫', '੬', '੭', '੮', '੯');
	}

	public function number() {
		return '310';
	}

	public function unicodeName() {
		return 'Gurmukhi';
	}
}
