<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LanguageKln - Representation of the Kalenjin language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKln extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'kln';
	}

	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
