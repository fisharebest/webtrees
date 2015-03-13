<?php namespace Fisharebest\Localization;

/**
 * Class LanguageRof - Representation of the Rombo language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageRof extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'rof';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTz;
	}
}
