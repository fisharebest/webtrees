<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBali - Representation of the Balinese script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBali extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Bali';
	}

	public function numerals() {
		return array('᭐', '᭑', '᭒', '᭓', '᭔', '᭕', '᭖', '᭗', '᭘', '᭙');
	}

	public function number() {
		return '360';
	}

	public function unicodeName() {
		return 'Balinese';
	}
}
