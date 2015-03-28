<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryUs;

/**
 * Class LanguageLkt - Representation of the Lakota language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLkt extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'lkt';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryUs;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule0;
	}
}
