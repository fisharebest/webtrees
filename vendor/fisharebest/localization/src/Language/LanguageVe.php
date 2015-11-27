<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryZa;

/**
 * Class LanguageVe - Representation of the Venda language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageVe extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 've';
	}

	public function defaultTerritory() {
		return new TerritoryZa;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
