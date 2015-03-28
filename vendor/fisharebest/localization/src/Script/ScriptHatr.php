<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHatr - Representation of the Hatran script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHatr extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Hatr';
	}

	/** {@inheritdoc} */
	public function number() {
		return '127';
	}
}
