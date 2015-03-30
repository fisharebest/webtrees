<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptRunr - Representation of the Runic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptRunr extends AbstractScript implements ScriptInterface {
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
