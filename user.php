<?php

/**
 * Created by PhpStorm.
 * User: alavi
 * Date: 8/4/17
 * Time: 8:13 PM
 */
class User
{
    private $user_id;
    private $level;
    private $message_id;
    private $user_firstname;
    private $text;
    private $db;

    function __construct($user_id, $message_id, $user_firstname, $text)
    {
        $this->db = mysqli_connect("localhost","root", "root", "sahlan_bot");
        $this->user_id = $user_id;
        $this->message_id = $message_id;
        $this->user_firstname = $user_firstname;
        $this->text = $text;
        $this->level = $this->getLevel();
    }

    private function sendMessage($text, $inline)
    {
        $this->makeCurl("sendMessage", ["chat_id" => $this->user_id, "text" => $text, "reply_markup" => json_encode([
            "inline_keyboard" =>

                $inline

        ])]);
    }

    private function editMessageText($text, $inline)
    {
        $this->makeCurl("editMessageText", ["message_id" => $this->message_id ,"chat_id" => $this->user_id, "text" => $text, "reply_markup" => json_encode([
            "inline_keyboard" =>

                $inline

        ])]);
    }

    private function getLevel()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.users WHERE user_id = {$this->user_id}");
        if ($row = mysqli_fetch_array($result))
            return $row['level'];
        else
        {
            mysqli_query($this->db, "INSERT INTO sahlan_bot.users (user_id, level, user_firstname) VALUES ({$this->user_id}, 'begin', '{$this->user_firstname}')");
            return 'begin';
        }
    }

    public function process()
    {
        if ($this->text == "/start")
            $this->starter();
        elseif ($this->level == "begin")

    }

    private function starter()
    {
        $this->sendMessage("با استفاده از منوی زیر، می توانید از محصولات و پروژه های ما دیدن فرمایید.", [
            [
                ["text" => "محصولات سهلان", "callback_data" => "Product_Us_Button"]
            ],
            [
                ["text" => "پروژه های سهلان", "callback_data" => "Project_Us_Button"]
            ],
            [
                ["text" => "درباره ی سهلان", "callback_data" => "About_Us_Button"]
            ],
            [
                ["text" => "تماس با سهلان", "callback_data" => "Contact_Us_Button"]
            ]
        ]);
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