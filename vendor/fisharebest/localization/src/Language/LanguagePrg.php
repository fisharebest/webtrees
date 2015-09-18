<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule3;
use Fisharebest\Localization\Territory\TerritoryLv;

/**
 * Class LanguagePrg - Representation of the Old Prussian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguagePrg extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'prg';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryLv;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule3;
	}
}
