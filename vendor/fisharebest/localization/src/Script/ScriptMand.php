<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMand - Representation of the Mandaic, Mandaean script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMand extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Mand';
	}

	/** {@inheritdoc} */
	public function number() {
		return '140';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Mandaic';
	}
}
