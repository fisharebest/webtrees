<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptGrek - Representation of the Greek script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGrek extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Grek';
	}

	public function number() {
		return '200';
	}

	public function unicodeName() {
		return 'Greek';
	}
}
