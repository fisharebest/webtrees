<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule15;
use Fisharebest\Localization\Script\ScriptCyrl;
use Fisharebest\Localization\Territory\TerritoryMk;

/**
 * Class LanguageMk - Representation of the Macedonian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMk extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'mk';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptCyrl;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMk;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule15;
	}
}
