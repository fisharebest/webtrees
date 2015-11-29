<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptPhlv - Representation of the Book Pahlavi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptPhlv extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Phlv';
	}

	public function number() {
		return '133';
	}
}
