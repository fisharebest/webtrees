<?php namespace Fisharebest\Localization;

/**
 * Class LanguageGl - Representation of the Galician language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageGl extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'gl';
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
