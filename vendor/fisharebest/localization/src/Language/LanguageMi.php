<?php namespace Fisharebest\Localization;

/**
 * Class LanguageEn - Representation of the Maori language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMi extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'mi';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNz;
	}
}
