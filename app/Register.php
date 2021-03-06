<?php

/**
 *  Generated by IceTea Framework 0.0.1
 *  Created at 2017-12-13 15:36:30
 *  Namespace App
 */

namespace App;

use PDO;
use IceTea\Database\DB;
use IceTea\Support\Model;

class Register extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function check($value, $field, $table = "users")
    {
        $st = DB::prepare("SELECT `{$field}` FROM `{$table}` WHERE `{$field}`=:bind LIMIT 1;");
        pc($st->execute([":bind" => $value]), $st);
        return (bool) $st->fetch(PDO::FETCH_NUM);
    }

    public static function insert($input)
    {
        $st = DB::prepare("INSERT INTO `users` (`username`, `email`, `password`, `registered_at`) VALUES (:username, :email, :password, :registered_at);");
        pc($st->execute(
            [
                ":username" => $input['username'],
                ":email"    => $input['email'],
                ":password"     => password_hash($input['password'], PASSWORD_BCRYPT),
                ":registered_at" => date("Y-m-d H:i:s")
            ]
        ), $st);
        $st = DB::prepare("SELECT `user_id` FROM `users` WHERE `username`=:username LIMIT 1;");
        pc($st->execute([':username' => $input['username']]), $st);
        $id = $st->fetch(PDO::FETCH_NUM);
        $st = DB::prepare("INSERT INTO `users_info` (`user_id`, `first_name`, `last_name`, `photo`, `bio`) VALUES (:id, :first_name, :last_name, NULL, NULL);");
        pc($st->execute(
            [
                ":id"           => $id[0],
                ":first_name"   => $input['first_name'],
                ":last_name"    => $input['last_name']
            ]
        ), $st);
        $st = DB::prepare("INSERT INTO `verification` (`user_id`, `token`, `expired_at`) VALUES (:user_id, :token, :expired_at);");
        $token = rstr(64);
        pc($st->execute(
            [
                ":user_id" => $id[0],
                ":token"   => $token,
                ":expired_at" => date("Y-m-d H:i:s", time() + (3600*24))
            ]
        ), $st);
        return $id[0];
    }
}
