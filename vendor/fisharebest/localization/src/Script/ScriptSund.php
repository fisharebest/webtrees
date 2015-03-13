<?php namespace Fisharebest\Localization;

/**
 * Class ScriptSund - Representation of the Sundanese script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSund extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Sund';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array(
			'0' => '᮰',
			'1' => '᮱',
			'2' => '᮲',
			'3' => '᮳',
			'4' => '᮴',
			'5' => '᮵',
			'6' => '᮶',
			'7' => '᮷',
			'8' => '᮸',
			'9' => '᮹',
		);
	}

	/** {@inheritdoc} */
	public function number() {
		return '362';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Sundanese';
	}
}
