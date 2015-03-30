<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSyrc - Representation of the Syriac script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSyrc extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Syrc';
	}

	/** {@inheritdoc} */
	public function number() {
		return '135';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Syriac';
	}
}
