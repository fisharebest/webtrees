<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptArmn - Representation of the Armenian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptArmn extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Armn';
	}

	/** {@inheritdoc} */
	public function number() {
		return '230';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Armenian';
	}
}
