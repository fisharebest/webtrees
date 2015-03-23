<?php namespace Fisharebest\Localization;

/**
 * Class LanguageEs - Representation of the Spanish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEs extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'es';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryEs;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
