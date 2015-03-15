<?php namespace Fisharebest\Localization;

/**
 * Class LanguageAf - Representation of the Afrikaans language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAf extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'af';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryZa;
	}
}
