<?php namespace Fisharebest\Localization;

/**
 * Class LanguageHsb - Representation of the Upper Sorbian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHsb extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'hsb';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryDe;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule10;
	}
}
