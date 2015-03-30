<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLimb - Representation of the Limbu script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLimb extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Limb';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '᥆',
			'1' => '᥇',
			'2' => '᥈',
			'3' => '᥉',
			'4' => '᥊',
			'5' => '᥋',
			'6' => '᥌',
			'7' => '᥍',
			'8' => '᥎',
			'9' => '᥏',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '336';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Limbu';
	}
}
