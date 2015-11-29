<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptThaa - Representation of the Thaana script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptThaa extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Thaa';
	}

	public function number() {
		return '170';
	}

	public function unicodeName() {
		return 'Thaana';
	}
}
