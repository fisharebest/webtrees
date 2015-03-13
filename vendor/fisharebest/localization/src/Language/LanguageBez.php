<?php namespace Fisharebest\Localization;

/**
 * Class LanguageBez - Representation of the Bena (Tanzania) language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageBez extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'bez';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryTz;
	}
}
