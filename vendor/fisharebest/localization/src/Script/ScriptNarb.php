<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNarb - Representation of the Old North Arabian (Ancient North Arabian) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptNarb extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Narb';
	}

	public function number() {
		return '106';
	}

	public function unicodeName() {
		return 'Old_North_Arabian';
	}
}
