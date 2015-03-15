<?php namespace Fisharebest\Localization;

/**
 * Class ScriptRunr - Representation of the Runic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptRunr extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Runr';
	}

	/** {@inheritdoc} */
	public function number() {
		return '211';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Runic';
	}
}
