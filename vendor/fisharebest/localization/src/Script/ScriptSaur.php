<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSaur - Representation of the Saurashtra script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSaur extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Saur';
	}

	public function numerals() {
		return array('꣐', '꣑', '꣒', '꣓', '꣔', '꣕', '꣖', '꣗', '꣘', '꣙');
	}

	public function number() {
		return '344';
	}

	public function unicodeName() {
		return 'Saurashtra';
	}
}
