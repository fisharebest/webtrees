<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptModi - Representation of the Modi, Moḍī script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptModi extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Modi';
	}

	public function number() {
		return '324';
	}

	public function unicodeName() {
		return 'Modi';
	}
}
