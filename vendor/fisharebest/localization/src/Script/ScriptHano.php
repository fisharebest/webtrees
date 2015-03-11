<?php namespace Fisharebest\Localization;

/**
 * Class ScriptHano - Representation of the Hanunoo (Hanunóo) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHano extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Hano';
	}

	/** {@inheritdoc} */
	public function number() {
		return '371';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Hanunoo';
	}
}
