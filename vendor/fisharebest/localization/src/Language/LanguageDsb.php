<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule10;
use Fisharebest\Localization\Territory\TerritoryDe;

/**
 * Class LanguageDsb - Representation of the Lower Sorbian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDsb extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'dsb';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryDe;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule10;
	}
}
