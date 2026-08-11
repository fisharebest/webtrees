<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Report;

/**
 * Definition of a single <Input> element collected from a report's setup
 * form.  Built incrementally by ParserSetup, then augmented by the HTTP
 * setup controller with the rendered $control / $extra HTML before being
 * passed to the view.
 *
 * Replaces the anonymous array{name, type, lookup, options, default,
 * value, extra} shape that used to be passed around.
 */
final readonly class InputDefinition
{
    public function __construct(
        public string $name,
        public string $type,
        public string $lookup,
        public string $options,
        public string $default,
        public string $value = '',
        public string $extra = '',
        public string $control = '',
    ) {
    }

    /**
     * Return a copy of this definition with the user-supplied value
     * captured from the XML body of <Input>...</Input>.
     */
    public function withValue(string $value): self
    {
        return new self(
            name:    $this->name,
            type:    $this->type,
            lookup:  $this->lookup,
            options: $this->options,
            default: $this->default,
            value:   $value,
            extra:   $this->extra,
            control: $this->control,
        );
    }

    /**
     * Return a copy of this definition with the rendered form-control
     * HTML supplied by the HTTP setup controller.
     */
    public function withControl(string $control, string $extra = ''): self
    {
        return new self(
            name:    $this->name,
            type:    $this->type,
            lookup:  $this->lookup,
            options: $this->options,
            default: $this->default,
            value:   $this->value,
            extra:   $extra !== '' ? $extra : $this->extra,
            control: $control,
        );
    }
}
