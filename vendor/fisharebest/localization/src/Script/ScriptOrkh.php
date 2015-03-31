<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptOrkh - Representation of the Old Turkic, Orkhon Runic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptOrkh extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Orkh';
	}

	/** {@inheritdoc} */
	public function number() {
		return '175';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Old_Turkic';
	}
}
