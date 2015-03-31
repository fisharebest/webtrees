<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptJava - Representation of the Javanese script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptJava extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Java';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '꧐',
			'1' => '꧑',
			'2' => '꧒',
			'3' => '꧓',
			'4' => '꧔',
			'5' => '꧕',
			'6' => '꧖',
			'7' => '꧗',
			'8' => '꧘',
			'9' => '꧙',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '361';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Javanese';
	}
}
