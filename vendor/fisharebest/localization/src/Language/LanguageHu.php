<?php namespace Fisharebest\Localization;

/**
 * Class LanguageHu - Representation of the Hungarian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHu extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'hu';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryHu;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
