<?php namespace Fisharebest\Localization;

/**
 * Class ScriptCopt - Representation of the Coptic script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptCopt extends Script {
	/** {@inheritdoc} */
	public function code() {
		return 'Copt';
	}

	/** {@inheritdoc} */
	public function number() {
		return '204';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Coptic';
	}
}
