<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSidd - Representation of the Siddham, Siddhaṃ, Siddhamātṛkā script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSidd extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Sidd';
	}

	/** {@inheritdoc} */
	public function number() {
		return '302';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Siddham';
	}
}
