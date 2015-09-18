<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBali - Representation of the Balinese script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBali extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Bali';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('᭐', '᭑', '᭒', '᭓', '᭔', '᭕', '᭖', '᭗', '᭘', '᭙');
	}

	/** {@inheritdoc} */
	public function number() {
		return '360';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Balinese';
	}
}
