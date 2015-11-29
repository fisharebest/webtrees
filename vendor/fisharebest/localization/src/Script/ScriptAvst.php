<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptAvst - Representation of the Avestan script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptAvst extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Avst';
	}

	public function number() {
		return '134';
	}

	public function unicodeName() {
		return 'Avestan';
	}
}
