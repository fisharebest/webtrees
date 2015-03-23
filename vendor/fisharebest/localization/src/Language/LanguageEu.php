<?php namespace Fisharebest\Localization;

/**
 * Class LanguageEu - Representation of the Basque language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEu extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'eu';
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
