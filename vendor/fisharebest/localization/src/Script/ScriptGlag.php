<?php namespace Fisharebest\Localization;

/**
 * Class ScriptGlag - Representation of the Glagolitic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGlag extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Glag';
	}

	/** {@inheritdoc} */
	public function number() {
		return '225';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Glagolitic';
	}
}
