<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBeng - Representation of the Bengali script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptBeng extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Beng';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯');
	}

	/** {@inheritdoc} */
	public function number() {
		return '325';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Bengali';
	}
}
