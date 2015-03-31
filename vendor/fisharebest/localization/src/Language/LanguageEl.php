<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptGrek;
use Fisharebest\Localization\Territory\TerritoryGr;

/**
 * Class LanguageEl - Representation of the Modern Greek (1453-) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageEl extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'el';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptGrek;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryGr;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
