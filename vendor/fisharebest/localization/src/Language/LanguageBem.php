<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryZm;

/**
 * Class LanguageBem - Representation of the Bemba (Zambia) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBem extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'bem';
	}

	public function defaultTerritory() {
		return new TerritoryZm;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
