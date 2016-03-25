<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptLoma - Representation of the Loma script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptLoma extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Loma';
	}

	public function number() {
		return '437';
	}
}
