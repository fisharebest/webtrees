<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptItal - Representation of the Old Italic (Etruscan, Oscan, etc.) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptItal extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Ital';
	}

	public function number() {
		return '210';
	}

	public function unicodeName() {
		return 'Old_Italic';
	}
}
