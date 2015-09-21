<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptMymr - Representation of the Myanmar script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptMymr extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Mymr';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('၀', '၁', '၂', '၃', '၄', '၅', '၆', '၇', '၈', '၉');
	}

	/** {@inheritdoc} */
	public function number() {
		return '350';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Myanmar';
	}
}
