<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryRw;

/**
 * Class LanguageRw - Representation of the Kinyarwanda language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageRw extends AbstractLanguage implements LanguageInterface {
	public function code() {
		return 'rw';
	}

	public function defaultTerritory() {
		return new TerritoryRw;
	}

	public function pluralRule() {
		return new PluralRule1;
	}
}
