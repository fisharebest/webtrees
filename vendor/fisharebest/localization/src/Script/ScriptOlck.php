<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptOlck - Representation of the Ol Chiki (Ol Cemet’, Ol, Santali) script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptOlck extends AbstractScript implements ScriptInterface {
	public function code() {
		return 'Olck';
	}

	public function numerals() {
		return array('᱐', '᱑', '᱒', '᱓', '᱔', '᱕', '᱖', '᱗', '᱘', '᱙');
	}

	public function number() {
		return '261';
	}

	public function unicodeName() {
		return 'Ol_Chiki';
	}
}
