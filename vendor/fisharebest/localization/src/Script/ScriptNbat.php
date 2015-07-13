<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNbat - Representation of the Nabataean script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptNbat extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Nbat';
	}

	/** {@inheritdoc} */
	public function number() {
		return '159';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Nabataean';
	}
}
