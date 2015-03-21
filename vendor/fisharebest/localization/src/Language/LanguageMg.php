<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMg - Representation of the Malagasy language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMg extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'mg';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMg;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule2;
	}
}
