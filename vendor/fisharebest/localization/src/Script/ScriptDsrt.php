<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptDsrt - Representation of the Deseret (Mormon) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptDsrt extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Dsrt';
	}

	/** {@inheritdoc} */
	public function number() {
		return '250';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Deseret';
	}
}
