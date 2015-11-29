<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptYiii - Representation of the Yi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptYiii extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Yiii';
	}

	public function number() {
		return '460';
	}

	public function unicodeName() {
		return 'Yi';
	}
}
