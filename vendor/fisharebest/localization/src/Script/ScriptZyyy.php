<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptZyyy - Representation of the Code for undetermined script script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptZyyy extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Zyyy';
	}

	/** {@inheritdoc} */
	public function number() {
		return '998';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Common';
	}
}
