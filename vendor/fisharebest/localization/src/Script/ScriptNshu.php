<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNshu - Representation of the Nüshu script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptNshu extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Nshu';
	}

	/** {@inheritdoc} */
	public function number() {
		return '499';
	}
}
