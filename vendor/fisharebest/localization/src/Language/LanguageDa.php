<?php namespace Fisharebest\Localization;

/**
 * Class LanguageDa - Representation of the Danish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDa extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'da';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryDk;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
