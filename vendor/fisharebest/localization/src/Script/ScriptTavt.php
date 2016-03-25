<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTavt - Representation of the Tai Viet script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTavt extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Tavt';
	}

	public function number() {
		return '359';
	}

	public function unicodeName() {
		return 'Tai_Viet';
	}
}
