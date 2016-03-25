<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSyrc - Representation of the Syriac script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSyrc extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Syrc';
	}

	public function number() {
		return '135';
	}

	public function unicodeName() {
		return 'Syriac';
	}
}
