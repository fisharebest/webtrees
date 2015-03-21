<?php namespace Fisharebest\Localization;

/**
 * Class LanguageNb - Representation of the Norwegian Bokmål language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNb extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'nb';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNo;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
