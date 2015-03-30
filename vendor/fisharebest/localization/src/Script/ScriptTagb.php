<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTagb - Representation of the Tagbanwa script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTagb extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Tagb';
	}

	/** {@inheritdoc} */
	public function number() {
		return '373';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Tagbanwa';
	}
}
