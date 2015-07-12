<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptOsge - Representation of the Osage script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptOsge extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Osge';
	}

	/** {@inheritdoc} */
	public function number() {
		return '219';
	}
}
