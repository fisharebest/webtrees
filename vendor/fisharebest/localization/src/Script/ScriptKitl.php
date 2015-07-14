<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKitl - Representation of the Khitan large script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKitl extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Kitl';
	}

	/** {@inheritdoc} */
	public function number() {
		return '505';
	}
}
