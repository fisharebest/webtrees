<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule7;
use Fisharebest\Localization\Script\ScriptCyrl;
use Fisharebest\Localization\Territory\TerritoryRs;

/**
 * Class LanguageSr - Representation of the Serbian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSr extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'sr';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptCyrl;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryRs;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule7;
	}
}
