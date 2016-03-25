<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptGeok - Representation of the Khutsuri (Asomtavruli and Nuskhuri) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGeok extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Geok';
	}

	public function number() {
		return '241';
	}

	public function unicodeName() {
		return 'Georgian';
	}
}
