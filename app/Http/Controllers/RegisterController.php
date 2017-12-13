<?php

/**
 *  Generated by IceTea Framework 0.0.1
 *  Created at 2017-11-18 13:14:19
 *  Namespace App\Http\Controllers
 */

namespace App\Http\Controllers;

use App\Register;
use IceTea\Http\Controller;

class RegisterController extends Controller
{
    /**
     * Show register view.
     */
    public function index()
    {
        return view("auth/register");
    }

    /**
     * Action
     */
    public function action()
    {
    	$input = file_get_contents("php://input");
        $input = json_decode($input, true);
        if (isset(
            $input['first_name'], 
            $input['last_name'], 
            $input['email'],
            $input['username'],
            $input['password'],
            $input['cpassword']
        ) && $this->isValidDevice()) {
            $this->validator($input);
        } else {
            abort(404);
        }
    }

    private function isValidDevice()
    {
        return true;
    }

    private function validator($input)
    {
        header("Content-type:application/json");
        filter_var($input['email'], FILTER_VALIDATE_EMAIL) or $this->err("Invalid email!");
        $len = strlen($input['username']);
        $len > 3  or $this->err("Username too short, please provide username more than 4 characters!");
        $len < 33 or $this->err("Username too long, please provide username less than 32 characters!");
        $input['password'] === $input['cpassword'] or $this->err("Confirm password does not match!");
        $len = strlen($input['password']);
        $len > 6 or $this->err("Password too short, please provide password more than 6 characters!");
        (!preg_match("#[^[:print:]]#", $input['password'])) or $this->err("Password must not contains unprintable chars!");
        if ($reg = Register::input($input)) {
            $this->suc("Register success!", $reg);
        } else {
            $this->err("Internal error!");
        }
    }

    private function err($msg)
    {
        http_response_code(400);
        exit($msg);
    }

    private function suc($msg, $reg)
    {
        http_response_code(200);
        setcookie("registered", base64_encode(json_encode($reg)), time()+300);
        exit($this->buildJson(
            [
                "status"    => "ok",
                "message"   => $msg,
                "redirect"  => "/register"
            ]
        ));
    }
}
