<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2024 webtrees development team
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

namespace Fisharebest\Webtrees\DB;

use Doctrine\DBAL\Types\AsciiStringType;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\DBAL\Types\Type;

/**
 * Simplify constructor arguments for doctrine/dbal.
 *
 * @internal
 */
enum ColumnType
{
    case Char;
    case Float;
    case Integer;
    case NChar;
    case NVarChar;
    case Text;
    case Timestamp;
    case VarChar;

    public static function toDBALType(ColumnType $column_type): Type
    {
        return match ($column_type) {
            self::Char      => new AsciiStringType(),
            self::Float     => new FloatType(),
            self::Integer   => new IntegerType(),
            self::NChar     => new StringType(),
            self::NVarChar  => new StringType(),
            self::Text      => new TextType(),
            self::Timestamp => new DateTimeImmutableType(),
            self::VarChar   => new AsciiStringType(),
        };
    }
}
