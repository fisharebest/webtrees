<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLana - Representation of the Tai Tham (Lanna) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLana extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Lana';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '᪀',
			'1' => '᪁',
			'2' => '᪂',
			'3' => '᪃',
			'4' => '᪄',
			'5' => '᪅',
			'6' => '᪆',
			'7' => '᪇',
			'8' => '᪈',
			'9' => '᪉',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '351';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Tai_Tham';
	}
}
