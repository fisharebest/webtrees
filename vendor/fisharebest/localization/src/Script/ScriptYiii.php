<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptYiii - Representation of the Yi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptYiii extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Yiii';
	}

	/** {@inheritdoc} */
	public function number() {
		return '460';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Yi';
	}
}
