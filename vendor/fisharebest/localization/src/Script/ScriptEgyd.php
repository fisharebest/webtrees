<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptEgyd - Representation of the Egyptian demotic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptEgyd extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Egyd';
	}

	/** {@inheritdoc} */
	public function number() {
		return '070';
	}
}
