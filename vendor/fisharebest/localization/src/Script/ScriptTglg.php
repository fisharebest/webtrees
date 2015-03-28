<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTglg - Representation of the Tagalog (Baybayin, Alibata) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTglg extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Tglg';
	}

	/** {@inheritdoc} */
	public function number() {
		return '370';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Tagalog';
	}
}
