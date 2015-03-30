<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptSyre - Representation of the Syriac (Estrangelo variant) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptSyre extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Syre';
	}

	/** {@inheritdoc} */
	public function number() {
		return '138';
	}
}
