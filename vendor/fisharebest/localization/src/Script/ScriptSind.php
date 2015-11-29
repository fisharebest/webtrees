<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSind - Representation of the Khudawadi, Sindhi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSind extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Sind';
	}

	public function number() {
		return '318';
	}

	public function unicodeName() {
		return 'Khudawadi';
	}
}
