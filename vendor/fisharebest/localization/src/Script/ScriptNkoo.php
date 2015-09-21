<?php namespace Fisharebest\Localization\Script;

/**
 * Class ScriptNkoo - Representation of the N’Ko script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class ScriptNkoo extends AbstractScript implements ScriptInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'Nkoo';
	}

	/** {@inheritdoc} */
	public function numerals() {
		return array('߀', '߁', '߂', '߃', '߄', '߅', '߆', '߇', '߈', '߉');
	}

	/** {@inheritdoc} */
	public function number() {
		return '165';
	}

	/** {@inheritdoc} */
	public function unicodeName() {
		return 'Nko';
	}
}
