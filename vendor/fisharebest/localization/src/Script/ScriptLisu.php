<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLisu - Representation of the Lisu (Fraser) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLisu extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Lisu';
	}

	/** {@inheritdoc} */
	public function number() {
		return '399';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Lisu';
	}
}
