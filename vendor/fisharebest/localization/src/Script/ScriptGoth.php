<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptGoth - Representation of the Gothic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGoth extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Goth';
	}

	public function number() {
		return '206';
	}

	public function unicodeName() {
		return 'Gothic';
	}
}
