<?php

/**
 * Created by PhpStorm.
 * User: alavi
 * Date: 8/4/17
 * Time: 8:13 PM
 */
class user
{
    private $user_id;
    private $level;
    private $message_id;
    private $user_firstname;
    private $text;
    private $db;

    function __construct()
    {
    }

    private function connectToDatabase()
    {
        $this->db = mysqli_connect("localhost","root", "root", "sahlan_bot");
    }

    public function getLevel()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.users WHERE user_id = {$this->user_id}");
        $row = mysqli_fetch_array($result);
        return $row['level'];
    }

    private function makeCurl($method,$datas=[])    //make and receive requests to bot
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://api.telegram.org/bot447776558:AAEeLmGEb6Nu06ltv41F3yTYBKdyFzslXcM/{$method}");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($datas));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        return $server_output;
    }
}