<?php namespace Fisharebest\Localization;

/**
 * Class LanguagePl - Representation of the Polish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguagePl extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'pl';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryPl;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule9;
	}
}
