<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryLu;

/**
 * Class LanguageLb - Representation of the Luxembourgish language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLb extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'lb';
	}

	public function defaultTerritory() {
		return new TerritoryLu;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
