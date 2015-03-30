<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLoma - Representation of the Loma script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLoma extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Loma';
	}

	/** {@inheritdoc} */
	public function number() {
		return '437';
	}
}
