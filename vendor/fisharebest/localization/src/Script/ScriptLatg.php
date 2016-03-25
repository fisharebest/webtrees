<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLatg - Representation of the Latin (Gaelic variant) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLatg extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Latg';
	}

	public function number() {
		return '216';
	}
}
