<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMymr - Representation of the Myanmar script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMymr extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Mymr';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '၀',
			'1' => '၁',
			'2' => '၂',
			'3' => '၃',
			'4' => '၄',
			'5' => '၅',
			'6' => '၆',
			'7' => '၇',
			'8' => '၈',
			'9' => '၉',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '350';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Myanmar';
	}
}
