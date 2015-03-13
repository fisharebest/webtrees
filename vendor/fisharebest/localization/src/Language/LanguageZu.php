<?php namespace Fisharebest\Localization;

/**
 * Class LanguageZu - Representation of the Zulu language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageZu extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'zu';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryZa;
	}
}
