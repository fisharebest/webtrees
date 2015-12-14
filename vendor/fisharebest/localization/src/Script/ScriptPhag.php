<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPhag - Representation of the Phags-pa script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPhag extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Phag';
	}

	public function number() {
		return '331';
	}

	public function unicodeName() {
		return 'Phags_Pa';
	}
}
