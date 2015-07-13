<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBass - Representation of the Bassa Vah script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBass extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Bass';
	}

	/** {@inheritdoc} */
	public function number() {
		return '259';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Bassa_Vah';
	}
}
