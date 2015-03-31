<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Script\ScriptSinh;
use Fisharebest\Localization\Territory\TerritoryLk;

/**
 * Class LanguageSi - Representation of the Sinhala language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSi extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'si';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptSinh;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryLk;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule2;
	}
}
