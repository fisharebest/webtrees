<?php namespace Fisharebest\Localization;

/**
 * Class ScriptOlck - Representation of the Ol Chiki (Ol Cemet’, Ol, Santali) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptOlck extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Olck';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '᱐',
			'1' => '᱑',
			'2' => '᱒',
			'3' => '᱓',
			'4' => '᱔',
			'5' => '᱕',
			'6' => '᱖',
			'7' => '᱗',
			'8' => '᱘',
			'9' => '᱙',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '261';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Ol_Chiki';
	}
}
