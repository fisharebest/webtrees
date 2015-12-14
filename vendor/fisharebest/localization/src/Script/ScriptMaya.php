<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMaya - Representation of the Mayan hieroglyphs script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMaya extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Maya';
	}

	public function number() {
		return '090';
	}
}
