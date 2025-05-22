<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
 * EVENT_OR_FACT_CLASSIFICATION := {Size=1:90}
 * A descriptive word or phrase used to further classify the parent event or
 * attribute tag. This should be used whenever either of the generic EVEN or
 * FACT tags are used. The value of this primitive is responsible for
 * classifying the generic event or fact being cited. For example, if the
 * attribute being defined was one of the persons skills, such as woodworking,
 * the FACT tag would have the value of `Woodworking', followed by a
 * subordinate TYPE tag with the value `Skills.'
 * 1 FACT Woodworking
 * 2 TYPE Skills
 * This groups the fact into a generic skills attribute, and in particular this
 * entry records the fact that this individual possessed the skill of
 * woodworking. Using the subordinate TYPE tag classification method with any
 * of the other defined event tags provides a further classification of the
 * parent tag but does not change the basic meaning of the parent tag. For
 * example, a MARR tag could be subordinated with a TYPE tag with an
 * EVENT_DESCRIPTOR value of `Common Law.'
 * 1 MARR
 * 2 TYPE Common Law
 * This classifies the entry as a common law marriage but the event is still a
 * marriage event. Other descriptor values might include, for example,
 * `stillborn' as a qualifier to BIRTh or `Tribal Custom' as a qualifier to
 * MARRiage.
 */
class EventOrFactClassification extends AbstractElement
{
    protected const int MAXIMUM_LENGTH = 90;
}
