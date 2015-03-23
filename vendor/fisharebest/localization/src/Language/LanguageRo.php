<?php namespace Fisharebest\Localization;

/**
 * Class LanguageRo - Representation of the Romanian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageRo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ro';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryRo;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule5;
	}
}
