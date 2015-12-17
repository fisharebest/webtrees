<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKore - Representation of the Korean (alias for Hangul + Han) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKore extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Kore';
	}

	public function number() {
		return '287';
	}
}
