<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptRjng - Representation of the Rejang (Redjang, Kaganga) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptRjng extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Rjng';
	}

	public function number() {
		return '363';
	}

	public function unicodeName() {
		return 'Rejang';
	}
}
