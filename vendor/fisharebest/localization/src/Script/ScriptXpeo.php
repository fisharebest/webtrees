<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptXpeo - Representation of the Old Persian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptXpeo extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Xpeo';
	}

	public function number() {
		return '030';
	}

	public function unicodeName() {
		return 'Old_Persian';
	}
}
