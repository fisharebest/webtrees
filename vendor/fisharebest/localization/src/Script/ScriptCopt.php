<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCopt - Representation of the Coptic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptCopt extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Copt';
	}

	public function number() {
		return '204';
	}

	public function unicodeName() {
		return 'Coptic';
	}
}
