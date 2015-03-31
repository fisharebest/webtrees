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
		return array(
			'0' => '߀',
			'1' => '߁',
			'2' => '߂',
			'3' => '߃',
			'4' => '߄',
			'5' => '߅',
			'6' => '߆',
			'7' => '߇',
			'8' => '߈',
			'9' => '߉',
		);
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
