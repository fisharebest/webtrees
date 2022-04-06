<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Elements;

/**
 * ROLE_IN_EVENT := {Size=1:15}
 * [ CHIL | HUSB | WIFE | MOTH | FATH | SPOU | (<ROLE_DESCRIPTOR>) ]
 * Indicates what role this person played in the event that is being cited in this context. For
 * example, if you cite a child's birth record as the source of the mother's name, the value for
 * this field is "MOTH." If you describe the groom of a marriage, the role is "HUSB." If the role
 * is something different than one of the six relationship role tags listed above then enclose the
 * role name within matching parentheses.
 */
class RoleInEvent extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 15;
}
