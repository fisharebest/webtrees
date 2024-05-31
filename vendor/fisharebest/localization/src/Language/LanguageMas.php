<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LanguageMas - Representation of the Masai language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageMas extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'mas';
    }

    public function defaultTerritory()
    {
        return new TerritoryKe();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
