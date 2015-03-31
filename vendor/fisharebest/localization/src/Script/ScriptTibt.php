<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTibt - Representation of the Tibetan script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTibt extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Tibt';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '༠',
			'1' => '༡',
			'2' => '༢',
			'3' => '༣',
			'4' => '༤',
			'5' => '༥',
			'6' => '༦',
			'7' => '༧',
			'8' => '༨',
			'9' => '༩',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '330';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Tibetan';
	}
}
