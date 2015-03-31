<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptGeok - Representation of the Khutsuri (Asomtavruli and Nuskhuri) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptGeok extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Geok';
	}

	/** {@inheritdoc} */
	public function number() {
		return '241';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Georgian';
	}
}
