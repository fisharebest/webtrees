<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTfng - Representation of the Tifinagh script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTfng extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Tfng';
	}

	/** {@inheritdoc} */
	public function number() {
		return '120';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Tifinagh';
	}
}
