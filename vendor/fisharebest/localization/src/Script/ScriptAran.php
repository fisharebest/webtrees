<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptAran - Representation of the Arabic (Nastaliq) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptAran extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Aran';
	}

	/** {@inheritdoc} */
	public function number() {
		return '161';
	}
}
