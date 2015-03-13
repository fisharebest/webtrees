<?php namespace Fisharebest\Localization;

/**
 * Class ScriptLepc - Representation of the Lepcha (Róng) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLepc extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Lepc';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '᱀',
			'1' => '᱁',
			'2' => '᱂',
			'3' => '᱃',
			'4' => '᱄',
			'5' => '᱅',
			'6' => '᱆',
			'7' => '᱇',
			'8' => '᱈',
			'9' => '᱉',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '335';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Lepcha';
	}
}
