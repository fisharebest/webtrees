<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptGeor - Representation of the Georgian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGeor extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Geor';
	}

	public function number() {
		return '240';
	}

	public function unicodeName() {
		return 'Georgian';
	}
}
