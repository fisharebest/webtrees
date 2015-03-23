<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMt - Representation of the Maltese language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMt extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'mt';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMt;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule13;
	}
}
