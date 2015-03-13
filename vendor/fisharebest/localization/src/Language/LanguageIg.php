<?php namespace Fisharebest\Localization;

/**
 * Class LanguageIg - Representation of the Igbo language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageIg extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ig';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryNg;
	}
}
