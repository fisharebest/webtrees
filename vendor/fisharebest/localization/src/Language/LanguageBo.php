<?php namespace Fisharebest\Localization;

/**
 * Class LanguageBo - Representation of the Tibetan language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBo extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'bo';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCn;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule0;
	}
}
