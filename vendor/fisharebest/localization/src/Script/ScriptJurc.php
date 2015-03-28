<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptJurc - Representation of the Jurchen script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptJurc extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Jurc';
	}

	/** {@inheritdoc} */
	public function number() {
		return '510';
	}
}
