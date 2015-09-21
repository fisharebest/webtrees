<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptOrya - Representation of the Oriya script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptOrya extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Orya';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('୦', '୧', '୨', '୩', '୪', '୫', '୬', '୭', '୮', '୯');
	}

	/** {@inheritdoc} */
	public function number() {
		return '327';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Oriya';
	}
}
