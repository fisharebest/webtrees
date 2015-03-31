<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptZsym - Representation of the Symbols script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptZsym extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Zsym';
	}

	/** {@inheritdoc} */
	public function number() {
		return '996';
	}
}
