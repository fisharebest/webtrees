<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKali - Representation of the Kayah Li script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKali extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Kali';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('꤀', '꤁', '꤂', '꤃', '꤄', '꤅', '꤆', '꤇', '꤈', '꤉');
	}

	/** {@inheritdoc} */
	public function number() {
		return '357';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Kayah_Li';
	}
}
