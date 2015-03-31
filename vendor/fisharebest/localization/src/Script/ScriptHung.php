<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHung - Representation of the Old Hungarian (Hungarian Runic) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHung extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Hung';
	}

	/** {@inheritdoc} */
	public function number() {
		return '176';
	}
}
