<?php namespace Fisharebest\Localization;

/**
 * Class ScriptArmn - Representation of the Armenian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptArmn extends Script {
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
