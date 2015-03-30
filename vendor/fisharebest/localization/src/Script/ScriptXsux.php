<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptXsux - Representation of the Cuneiform, Sumero-Akkadian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptXsux extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Xsux';
	}

	/** {@inheritdoc} */
	public function number() {
		return '020';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Cuneiform';
	}
}
