<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCprt - Representation of the Cypriot script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptCprt extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Cprt';
	}

	public function number() {
		return '403';
	}

	public function unicodeName() {
		return 'Cypriot';
	}
}
