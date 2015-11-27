<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTale - Representation of the Tai Le script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTale extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Tale';
	}

	public function number() {
		return '353';
	}

	public function unicodeName() {
		return 'Tai_Le';
	}
}
