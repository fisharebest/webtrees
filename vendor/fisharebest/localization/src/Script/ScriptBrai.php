<?php namespace Fisharebest\Localization;

/**
 * Class ScriptBrai - Representation of the Braille script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBrai extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Brai';
	}

	/** {@inheritdoc} */
	public function number() {
		return '570';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Braille';
	}
}
