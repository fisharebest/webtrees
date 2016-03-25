<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryBi;

/**
 * Class LanguageRn - Representation of the Rundi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageRn extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'rn';
	}

	public function defaultTerritory() {
		return new TerritoryBi;
	}
}
