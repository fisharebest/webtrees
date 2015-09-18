<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSund - Representation of the Sundanese script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSund extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Sund';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('᮰', '᮱', '᮲', '᮳', '᮴', '᮵', '᮶', '᮷', '᮸', '᮹');
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
