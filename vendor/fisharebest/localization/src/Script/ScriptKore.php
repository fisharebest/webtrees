<?php namespace Fisharebest\Localization;

/**
 * Class ScriptKore - Representation of the Korean (alias for Hangul + Han) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKore extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Kore';
	}

	/** {@inheritdoc} */
	public function number() {
		return '287';
	}
}
