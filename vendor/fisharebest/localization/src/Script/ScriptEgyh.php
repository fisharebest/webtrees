<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptEgyh - Representation of the Egyptian hieratic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptEgyh extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Egyh';
	}

	public function number() {
		return '060';
	}
}
