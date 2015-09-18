<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptKnda - Representation of the Kannada script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptKnda extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Knda';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('೦', '೧', '೨', '೩', '೪', '೫', '೬', '೭', '೮', '೯');
	}

	/** {@inheritdoc} */
	public function number() {
		return '345';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Kannada';
	}
}
