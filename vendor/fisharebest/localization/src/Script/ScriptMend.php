<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMend - Representation of the Mende Kikakui script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMend extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Mend';
	}

	/** {@inheritdoc} */
	public function number() {
		return '438';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Mende_Kikakui';
	}
}
