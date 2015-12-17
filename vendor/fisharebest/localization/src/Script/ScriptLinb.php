<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLinb - Representation of the Linear B script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLinb extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Linb';
	}

	public function number() {
		return '401';
	}

	public function unicodeName() {
		return 'Linear_B';
	}
}
