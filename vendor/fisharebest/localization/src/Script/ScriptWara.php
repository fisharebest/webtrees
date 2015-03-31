<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptWara - Representation of the Warang Citi (Varang Kshiti) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptWara extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Wara';
	}

	/** {@inheritdoc} */
	public function number() {
		return '262';
	}
}
