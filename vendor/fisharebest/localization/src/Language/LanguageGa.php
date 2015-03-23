<?php namespace Fisharebest\Localization;

/**
 * Class LanguageGa - Representation of the Irish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGa extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ga';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIe;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule11;
	}
}
