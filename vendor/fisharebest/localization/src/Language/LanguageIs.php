<?php namespace Fisharebest\Localization;

/**
 * Class LanguageIs - Representation of the Icelandic language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageIs extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'is';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIs;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule15;
	}
}
