<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSamr - Representation of the Samaritan script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSamr extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Samr';
	}

	/** {@inheritdoc} */
	public function number() {
		return '123';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Samaritan';
	}
}
