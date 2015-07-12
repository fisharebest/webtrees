<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptHmng - Representation of the Pahawh Hmong script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptHmng extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Hmng';
	}

	/** {@inheritdoc} */
	public function number() {
		return '450';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Pahawh_Hmong';
	}
}
