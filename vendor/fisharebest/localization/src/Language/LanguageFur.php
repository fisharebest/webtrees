<?php namespace Fisharebest\Localization;

/**
 * Class LanguageFur - Representation of the Friulian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageFur extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'fur';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIt;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
