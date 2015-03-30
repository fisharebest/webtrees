<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBopo - Representation of the Bopomofo script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBopo extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Bopo';
	}

	/** {@inheritdoc} */
	public function number() {
		return '285';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Bopomofo';
	}
}
