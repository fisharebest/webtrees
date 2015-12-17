<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptUgar - Representation of the Ugaritic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptUgar extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Ugar';
	}

	public function number() {
		return '040';
	}

	public function unicodeName() {
		return 'Ugaritic';
	}
}
