<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptCyrl;
use Fisharebest\Localization\Territory\TerritoryBg;

/**
 * Class LanguageBg - Representation of the Bulgarian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBg extends AbstractLanguage implements LanguageInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'bg';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptCyrl;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryBg;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule1;
	}
}
