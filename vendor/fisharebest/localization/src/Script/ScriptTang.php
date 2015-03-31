<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTang - Representation of the Tangut script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTang extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Tang';
	}

	/** {@inheritdoc} */
	public function number() {
		return '520';
	}
}
