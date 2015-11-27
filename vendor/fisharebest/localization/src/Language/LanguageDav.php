<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LanguageDav - Representation of the Taita language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDav extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'dav';
	}

	public function defaultTerritory() {
		return new TerritoryKe;
	}
}
