<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHans - Representation of the Simplified Han script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHans extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Hans';
	}

	/** {@inheritdoc} */
	public function number() {
		return '501';
	}
}
