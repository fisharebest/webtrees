<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMahj - Representation of the Mahajani script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMahj extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Mahj';
	}

	/** {@inheritdoc} */
	public function number() {
		return '314';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Mahajani';
	}
}
