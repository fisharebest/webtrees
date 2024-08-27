<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees;

use Closure;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Illuminate\Database\Capsule\Manager as DB;
use PragmaRX\Google2FA\Google2FA;
use chillerlan\QRCode\QRCode;
	

use function is_string;

/**
 * Provide an interface to the wt_user table.
 */
class User implements UserInterface
{
    private int $user_id;

    private string $user_name;

    private string $real_name;

    private string $email;

    /** @var array<string,string> */
    private array $preferences;

    /**
     * @param int    $user_id
     * @param string $user_name
     * @param string $real_name
     * @param string $email
     */
    public function __construct(int $user_id, string $user_name, string $real_name, string $email)
    {
        $this->user_id   = $user_id;
        $this->user_name = $user_name;
        $this->real_name = $real_name;
        $this->email     = $email;

        $this->preferences = DB::table('user_setting')
            ->where('user_id', '=', $this->user_id)
            ->pluck('setting_value', 'setting_name')
            ->all();
    }

    /**
     * The user‘s internal identifier.
     *
     * @return int
     */
    public function id(): int
    {
        return $this->user_id;
    }

    /**
     * The users email address.
     *
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * Set the email address of this user.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        if ($this->email !== $email) {
            $this->email = $email;

            DB::table('user')
                ->where('user_id', '=', $this->user_id)
                ->update([
                    'email' => $email,
                ]);
        }

        return $this;
    }

    /**
     * The user‘s real name.
     *
     * @return string
     */
    public function realName(): string
    {
        return $this->real_name;
    }

    /**
     * Generate a QR code image based on 2FA secret and return both.
     *
     * @return array
     */

    public function genQRcode(): array
    {
	$qrinfo = array();
	$google2fa = new Google2FA();
        $qrinfo['secret'] = $google2fa->generateSecretKey();	
        $data = 'otpauth://totp/test?secret=' . $qrinfo['secret'] . '&issuer=webtrees.bmsgenie.net'; 	    
	$qrinfo['qrcode'] = (new QRCode)->render($data);
	return $qrinfo;
    }

    /**
     * Set the real name of this user.
     *
     * @param string $real_name
     *
     * @return User
     */
    public function setRealName(string $real_name): User
    {
        if ($this->real_name !== $real_name) {
            $this->real_name = $real_name;

            DB::table('user')
                ->where('user_id', '=', $this->user_id)
                ->update([
                    'real_name' => $real_name,
                ]);
        }

        return $this;
    }

    /**
     * The user‘s login name.
     *
     * @return string
     */
    public function userName(): string
    {
        return $this->user_name;
    }

    /**
     * Set the login name for this user.
     *
     * @param string $user_name
     *
     * @return self
     */
    public function setUserName(string $user_name): self
    {
        if ($this->user_name !== $user_name) {
            $this->user_name = $user_name;

            DB::table('user')
                ->where('user_id', '=', $this->user_id)
                ->update([
                    'user_name' => $user_name,
                ]);
        }

        return $this;
    }

    /**
     * Fetch a user option/setting from the wt_user_setting table.
     * Since we'll fetch several settings for each user, and since there aren't
     * that many of them, fetch them all in one database query
     *
     * @param string $setting_name
     * @param string $default
     *
     * @return string
     */
    public function getPreference(string $setting_name, string $default = ''): string
    {
        return $this->preferences[$setting_name] ?? $default;
    }

    /**
     * Update a setting for the user.
     *
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return void
     */
    public function setPreference(string $setting_name, string $setting_value): void
    {
        if ($this->getPreference($setting_name) !== $setting_value) {
            DB::table('user_setting')->updateOrInsert([
                'user_id'      => $this->user_id,
                'setting_name' => $setting_name,
            ], [
                'setting_value' => $setting_value,
            ]);

            $this->preferences[$setting_name] = $setting_value;
        }
    }

    /**
     * Set the password of this user.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        DB::table('user')
            ->where('user_id', '=', $this->user_id)
            ->update([
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);

        return $this;
    }
    /**
     * Set the secret of this user.
     *
     * @param string $secret
     *
     * @return User
     */
    public function setSecret(string $secret): User
    {
        DB::table('user')
            ->where('user_id', '=', $this->user_id)
            ->update([
                'secret' => $secret,
            ]);

        return $this;
    }

    /**
     * Validate a supplied password
     *
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        $password_hash = DB::table('user')
            ->where('user_id', '=', $this->id())
            ->value('password');

        if (is_string($password_hash) && password_verify($password, $password_hash)) {
            if (password_needs_rehash($password_hash, PASSWORD_DEFAULT)) {
                $this->setPassword($password);
            }

            return true;
        }

        return false;
    }
/**
     * Validate a supplied 2fa code
     *
     * @param string $code2fa
     *
     * @return bool
     */
    public function check2facode(string $code2fa): bool
    {
        $secret = DB::table('user')
            ->where('user_id', '=', $this->id())
            ->value('secret');
	$google2fa = new \PragmaRX\Google2FA\Google2FA();		
        if($google2fa->verifyKey($secret, $code2fa)) {
		   return true;
		}
        return false;
    }
    

    /**
     * A closure which will create an object from a database row.
     *
     * @return Closure(object):User
     */
    public static function rowMapper(): Closure
    {
        return static fn (object $row): User => new self((int) $row->user_id, $row->user_name, $row->real_name, $row->email);
    }
}
