<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMong - Representation of the Mongolian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMong extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Mong';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '᠐',
			'1' => '᠑',
			'2' => '᠒',
			'3' => '᠓',
			'4' => '᠔',
			'5' => '᠕',
			'6' => '᠖',
			'7' => '᠗',
			'8' => '᠘',
			'9' => '᠙',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '145';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Mongolian';
	}
}
