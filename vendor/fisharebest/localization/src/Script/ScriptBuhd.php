<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBuhd - Representation of the Buhid script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBuhd extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Buhd';
	}

	/** {@inheritdoc} */
	public function number() {
		return '372';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Buhid';
	}
}
