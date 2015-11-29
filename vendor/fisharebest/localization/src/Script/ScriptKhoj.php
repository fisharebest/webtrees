<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKhoj - Representation of the Khojki script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKhoj extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Khoj';
	}

	public function number() {
		return '322';
	}

	public function unicodeName() {
		return 'Khojki';
	}
}
