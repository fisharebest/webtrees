<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptTelu - Representation of the Telugu script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptTelu extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Telu';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('౦', '౧', '౨', '౩', '౪', '౫', '౬', '౭', '౮', '౯');
	}

	/** {@inheritdoc} */
	public function number() {
		return '340';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Telugu';
	}
}
