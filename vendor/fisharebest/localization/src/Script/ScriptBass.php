<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBass - Representation of the Bassa Vah script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBass extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Bass';
	}

	public function number() {
		return '259';
	}

	public function unicodeName() {
		return 'Bassa_Vah';
	}
}
