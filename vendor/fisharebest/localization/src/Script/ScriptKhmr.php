<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKhmr - Representation of the Khmer script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKhmr extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Khmr';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩');
	}

	/** {@inheritdoc} */
	public function number() {
		return '355';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Khmer';
	}
}
