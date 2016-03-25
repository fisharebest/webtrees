<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryTz;

/**
 * Class LanguageAsa - Representation of the Asu (Tanzania) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAsa extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'asa';
	}

	public function defaultTerritory() {
		return new TerritoryTz;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
