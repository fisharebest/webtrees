<?php namespace Fisharebest\Localization;

/**
 * Class ScriptTalu - Representation of the New Tai Lue script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTalu extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Talu';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '᧐',
			'1' => '᧑',
			'2' => '᧒',
			'3' => '᧓',
			'4' => '᧔',
			'5' => '᧕',
			'6' => '᧖',
			'7' => '᧗',
			'8' => '᧘',
			'9' => '᧙',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '354';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'New_Tai_Lue';
	}
}
