<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLaoo - Representation of the Lao script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLaoo extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Laoo';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '໐',
			'1' => '໑',
			'2' => '໒',
			'3' => '໓',
			'4' => '໔',
			'5' => '໕',
			'6' => '໖',
			'7' => '໗',
			'8' => '໘',
			'9' => '໙',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '356';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Lao';
	}
}
