<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKits - Representation of the Khitan small script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKits extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Kits';
	}

	/** {@inheritdoc} */
	public function number() {
		return '288';
	}
}
