<?php namespace Fisharebest\Localization;

/**
 * Class LanguageHr - Representation of the Croatian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHr extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'hr';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryHr;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule7;
	}
}
