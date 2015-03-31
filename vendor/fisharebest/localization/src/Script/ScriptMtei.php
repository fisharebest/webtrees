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
		return array(
			'0' => '꯰',
			'1' => '꯱',
			'2' => '꯲',
			'3' => '꯳',
			'4' => '꯴',
			'5' => '꯵',
			'6' => '꯶',
			'7' => '꯷',
			'8' => '꯸',
			'9' => '꯹',
		);
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
