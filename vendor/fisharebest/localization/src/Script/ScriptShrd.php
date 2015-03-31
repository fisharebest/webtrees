<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptShrd - Representation of the Sharada, Śāradā script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptShrd extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Shrd';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '𑇐',
			'1' => '𑇑',
			'2' => '𑇒',
			'3' => '𑇓',
			'4' => '𑇔',
			'5' => '𑇕',
			'6' => '𑇖',
			'7' => '𑇗',
			'8' => '𑇘',
			'9' => '𑇙',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '319';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Sharada';
	}
}
