<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptOrya - Representation of the Oriya script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptOrya extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Orya';
	}

	public function numerals() {
		return array('୦', '୧', '୨', '୩', '୪', '୫', '୬', '୭', '୮', '୯');
	}

	public function number() {
		return '327';
	}

	public function unicodeName() {
		return 'Oriya';
	}
}
