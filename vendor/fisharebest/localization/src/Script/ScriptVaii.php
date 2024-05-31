<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptVaii - Representation of the Vai script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptVaii extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Vaii';
    }

    public function numerals()
    {
        return array('꘠', '꘡', '꘢', '꘣', '꘤', '꘥', '꘦', '꘧', '꘨', '꘩');
    }

    public function number()
    {
        return '470';
    }

    public function unicodeName()
    {
        return 'Vai';
    }
}
