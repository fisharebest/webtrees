<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;

/**
 * Class LanguageIa - Representation of the Interlingua (International Auxiliary Abstract Language) language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageIa extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ia';
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
