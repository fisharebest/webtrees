<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptVisp - Representation of the Visible Speech script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptVisp extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Visp';
	}

	/** {@inheritdoc} */
	public function number() {
		return '280';
	}
}
