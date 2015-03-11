<?php namespace Fisharebest\Localization;

/**
 * Class LanguageNnh - Representation of the Ngiemboon language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageNnh extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'nnh';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
