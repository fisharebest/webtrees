<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptArmi - Representation of the Imperial Aramaic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptArmi extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Armi';
	}

	public function number() {
		return '124';
	}

	public function unicodeName() {
		return 'Imperial_Aramaic';
	}
}
