<?php namespace Fisharebest\Localization;

/**
 * Class LanguageHaw - Representation of the Hawaiian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHaw extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'haw';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryUs;
	}
}
