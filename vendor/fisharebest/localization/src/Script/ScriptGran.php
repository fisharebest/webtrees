<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptGran - Representation of the Grantha script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGran extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Gran';
	}

	public function number() {
		return '343';
	}

	public function unicodeName() {
		return 'Grantha';
	}
}
