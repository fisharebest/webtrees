<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMroo - Representation of the Mro, Mru script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMroo extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Mroo';
	}

	public function number() {
		return '199';
	}

	public function unicodeName() {
		return 'Mro';
	}
}
