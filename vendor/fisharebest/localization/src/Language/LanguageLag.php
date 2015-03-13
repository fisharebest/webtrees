<?php namespace Fisharebest\Localization;

/**
 * Class LanguageLag - Representation of the Langi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLag extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'lag';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTz;
	}
}
