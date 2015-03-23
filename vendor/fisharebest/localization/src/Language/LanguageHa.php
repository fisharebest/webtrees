<?php namespace Fisharebest\Localization;

/**
 * Class LanguageHa - Representation of the Hausa language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHa extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ha';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNg;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
