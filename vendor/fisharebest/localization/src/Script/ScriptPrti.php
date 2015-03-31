<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPrti - Representation of the Inscriptional Parthian script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPrti extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Prti';
	}

	/** {@inheritdoc} */
	public function number() {
		return '130';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Inscriptional_Parthian';
	}
}
