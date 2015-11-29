<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBamu - Representation of the Bamum script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBamu extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Bamu';
	}

	public function number() {
		return '435';
	}

	public function unicodeName() {
		return 'Bamum';
	}
}
