<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LanguageEbu - Representation of the Embu language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEbu extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'ebu';
	}

	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
