<?php namespace Fisharebest\Localization;

/**
 * Class LanguagePt - Representation of the Portuguese language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguagePt extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'pt';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryPt;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
