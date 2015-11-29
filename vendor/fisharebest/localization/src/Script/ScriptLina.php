<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLina - Representation of the Linear A script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLina extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Lina';
	}

	public function number() {
		return '400';
	}

	public function unicodeName() {
		return 'Linear_A';
	}
}
