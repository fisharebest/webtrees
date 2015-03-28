<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptThaa - Representation of the Thaana script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptThaa extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Thaa';
	}

	/** {@inheritdoc} */
	public function number() {
		return '170';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Thaana';
	}
}
