<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Webtrees\Database;

/**
 * Upgrade the database schema from version 37 to version 38.
 */
class Migration37 implements MigrationInterface
{
    /**
     * Upgrade to to the next version
     */
    public function upgrade()
    {
        // Move repositories to their own table
        Database::exec(
            "CREATE TABLE IF NOT EXISTS `##repository` (" .
            " repository_id INTEGER AUTO_INCREMENT                      NOT NULL," .
            " gedcom_id     INTEGER                                     NOT NULL," .
            " xref          VARCHAR(20)                                 NOT NULL," .
            " gedcom        LONGTEXT                                    NOT NULL," .
            " name          VARCHAR(90)                                 NOT NULL," .
            " address       VARCHAR(255)                                NOT NULL," .
            " restriction   ENUM('', 'confidential', 'privacy', 'none') NOT NULL," .
            " uid           VARCHAR(34)                                 NOT NULL," .
            " changed_at    DATETIME                                    NOT NULL," .
            " PRIMARY KEY (repository_id)," .
            " UNIQUE  KEY `##repository_ix1` (gedcom_id, xref)," .
            " UNIQUE  KEY `##repository_ix2` (xref, gedcom_id)," .
            "         KEY `##repository_ix3` (name)," .
            "         KEY `##repository_ix4` (address)," .
            "         KEY `##repository_ix5` (restriction)," .
            "         KEY `##repository_ix6` (uid)," .
            "         KEY `##repository_ix7` (changed_at)," .
            " CONSTRAINT `##repository_fk1` FOREIGN KEY (gedcom_id) REFERENCES `##gedcom` (gedcom_id)" .
            ") COLLATE utf8_unicode_ci ENGINE=InnoDB"
        );

        Database::exec("START TRANSACTION");

        $repositories = Database::prepare("SELECT * FROM `##other` WHERE o_type = 'REPO'")->fetchAll();

        foreach ($repositories as $n => $repository) {
            Database::prepare(
                "INSERT INTO `##repository` (" .
                " gedcom_id, xref, gedcom, name, address, restriction, uid, changed_at" .
                ") VALUES (" .
                " :gedcom_id, :xref, :gedcom, :name, :address, :restriction, :uid, :changed_at" .
                ")"
            )->execute(array(
                'gedcom_id'   => $repository->o_file,
                'xref'        => $repository->o_id,
                'gedcom'      => $repository->o_gedcom,
                'name'        => '',
                'address'     => '',
                'restriction' => '',
                'uid'         => '',
                'changed_at'  => '',
            ));

            Database::prepare(
                "DELETE FROM `##other` WHERE o_file = :gedcom_id AND o_id = :xref"
            )->execute(array(
                'gedcom_id' => $repository->o_file,
                'xref'      => $repository->o_id,
            ));

            if ($n % 500 === 499) {
                Database::exec("COMMIT");
                Database::exec("START TRANSACTION");

            }
        }

        Database::exec("COMMIT");
    }
}
