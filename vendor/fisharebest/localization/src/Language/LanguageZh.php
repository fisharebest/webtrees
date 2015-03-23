<?php namespace Fisharebest\Localization;

/**
 * Class LanguageZh - Representation of the Chinese language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageZh extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'zh';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCn;
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptHans;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule0;
	}
}
