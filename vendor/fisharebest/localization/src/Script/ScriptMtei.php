<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMtei - Representation of the Meitei Mayek (Meithei, Meetei) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMtei extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Mtei';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('꯰', '꯱', '꯲', '꯳', '꯴', '꯵', '꯶', '꯷', '꯸', '꯹');
	}

	/** {@inheritdoc} */
	public function number() {
		return '337';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Meetei_Mayek';
	}
}
