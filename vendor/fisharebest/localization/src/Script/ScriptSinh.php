<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSinh - Representation of the Sinhala script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSinh extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Sinh';
	}

	/** {@inheritdoc} */
	public function number() {
		return '348';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Sinhala';
	}
}
