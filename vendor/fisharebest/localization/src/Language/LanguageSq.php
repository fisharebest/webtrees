<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSq - Representation of the Albanian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSq extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'sq';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryAl;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
