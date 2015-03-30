<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPlrd - Representation of the Miao (Pollard) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPlrd extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Plrd';
	}

	/** {@inheritdoc} */
	public function number() {
		return '282';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Miao';
	}
}
