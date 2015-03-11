<?php namespace Fisharebest\Localization;

/**
 * Class ScriptLatf - Representation of the Latin (Fraktur variant) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLatf extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Latf';
	}

	/** {@inheritdoc} */
	public function number() {
		return '217';
	}
}
