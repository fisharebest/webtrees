<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPrti - Representation of the Inscriptional Parthian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPrti extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Prti';
	}

	public function number() {
		return '130';
	}

	public function unicodeName() {
		return 'Inscriptional_Parthian';
	}
}
