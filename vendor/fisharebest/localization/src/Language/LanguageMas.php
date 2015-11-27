<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LanguageMas - Representation of the Masai language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMas extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'mas';
	}

	public function defaultTerritory() {
		return new TerritoryKe;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
