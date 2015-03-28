<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSgnw - Representation of the SignWriting script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSgnw extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Sgnw';
	}

	/** {@inheritdoc} */
	public function number() {
		return '095';
	}
}
