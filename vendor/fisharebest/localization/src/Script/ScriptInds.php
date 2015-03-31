<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptInds - Representation of the Indus (Harappan) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptInds extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Inds';
	}

	/** {@inheritdoc} */
	public function number() {
		return '610';
	}
}
