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
    private $emailStatus;

    function __construct($user_id, $message_id, $user_firstname, $text)
    {
        $this->db = mysqli_connect("localhost","root", "root", "sahlan_bot");
        $this->user_id = $user_id;
        $this->message_id = $message_id;
        $this->user_firstname = $user_firstname;
        $this->text = $text;
        $this->level = $this->getLevel();
        $this->emailStatus = $this->getEmailStatus();
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
        if ($this->emailStatus == "getting_email")
            mysqli_fetch_array($this->db, "UPDATE sahlan_bot.users SET email = '{$this->text}', email_status = NULL WHERE user_id = {$this->user_id}");
        if ($this->text == "/start")
            $this->starter();
        elseif ($this->level == "begin")
            $this->beginner();
        elseif ($this->level == "product_showed")
            $this->productManager();
        elseif ($this->level == "office_partition_showed")
            $this->officePartitionManager();
        elseif ($this->level == "classic_partition_showed")
            $this->classicPartitionManager();

    }

    private function setEmailStatus($status)
    {
        mysqli_query($this->db, "UPDATE sahlan_bot.users SET email_status = '{$status}'");
    }

    private function getEmailStatus()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.users WHERE user_id = {$this->user_id}");
        $row = mysqli_fetch_array($result);
        return $row['email_status'];
    }

    private function emailGetting()
    {
        $this->setEmailStatus("getting_email");
        $this->editMessageText("برای دریافت کاتالوگ ایمیل خود را وارد کنید.", []);
    }

    private function classicPartitionManager()
    {
        if (!$this->checkMail())
            $this->emailGetting();
        else
        {
            if ($this->text == "Get_Catalog")
            {
                $this->editMessageText("فایل را دریافت کنید")
            }
        }
    }
    private function getClassicPartitionCatalog()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.catalog");
        $row = mysqli_fetch_array($result);
        return $row['classic_partition'];
    }

    private function sendDocument($url)
    {
        $this->makeCurl("sendDocument", ["chat_id" => $this->user_id, "document" => $url]);
    }

    private function setLevel($level)
    {
        mysqli_query($this->db,"UPDATE sahlan_bot.users SET level = '{$level}' WHERE user_id = {$this->user_id}");
    }

    private function checkMail()
    {
        $result = mysqli_query($this->db, "SELECT email FROM sahlan_bot.users WHERE user_id = {$this->user_id}");
        $row = mysqli_fetch_array($result);
        if ($row)
            return true;
        else
            return false;
    }

    private function officePartitionManager()
    {
        if ($this->text == "Classic_Partition")
            $this->showClassicPartition();
    }

    private function showClassicPartition()
    {
        $this->setLevel("classic_partition_showed");
        $this->editMessageText("ارتیشن کلاسیک سهلان به گونه طراحی شده است که دفتر کاري شما در ذهن ها ماندگار شود. طراحی هوشمند و تطابق با
جدیدترین متدهاي طراحی فضاي اداري، اصلی ترین ویژگی هاي پارتیشن کلاسیک محسوب می شوند", [
            [
                ["text" => "دریافت کاتالوگ", "callback_data" => "Get_Catalog"]
            ],
            [
                ["text" => "پروژه های مرتبط", "callback_data" => "Related_Projects"]
            ],
            [
                ["text" => "تماس با ما", "callback_data" => "Contact_Us"]
            ],
            [
                ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
            ]
        ]);
    }

    private function beginner()
    {
        if ($this->text == "Product_Us_Button")
            $this->showProduct();
        elseif ($this->text == "Project_Us_Button")
            $this->showProject();
        elseif ($this->text == "About_Us_Button")
            $this->showAbout();
        elseif ($this->text == "Contact_Us_Button")
            $this->contactUs();

    }

    private function productManager()
    {
        if ($this->text == "Office_Partition")
            $this->showOfficePartition();
    }

    private function showOfficePartition()
    {
        $this->setLevel("office_partition_showed");
        $this->editMessageText("پارتیشن هاي اداري سهلان به دو دسته پارتیشن هاي کلاسیک و پارتیشن هاي اداري تقسیم می شوند.
با انتخاب هر مورد می توانید کاتالوگ محصول را دریافت کنید و پروژه هاي مرتبط با آن محصول را مشاهده نمایید.", [
    [
        ["text" => "پارتیشن کلاسیک", "callback_data" => "Classic_Partition"]
    ],
            [
                ["text" => "پارتیشن هاي دوجداره و فریملس", "callback_data" => "Partition_Frimls"]
            ]
        ]);
    }

    private function showProduct()
    {
        $this->setLevel("product_showed");
        $this->editMessageText("محصولات سهلان به دو دسته کلی دکوراسیون اداری و سازه های نمایشگاهی تقیسم می شود.
شم می توانید با استفاده از منوی زیر کاتالوگ های هر محصول را در یافت کنید و پروژه های مربوط به آن را مشاهده نمایید.",[
            [
                ["text" => "پارتیشن های اداری", "callback_data" => "Office_Partition"]
            ],
            [
                ["text" => "مبلمان اداری", "callback_data" => "Office_Couch"]
            ],
            [
                ["text" => "تجهیزات اداری", "callback_data" => "Office_Supply"]
            ],
            [
                ["text" => "سازه های نمایشگاهی", "callback_data" => "Exhibition_Structure"]
            ]
        ]);
    }

    private function showProject()
    {

    }

    private function showAbout()
    {

    }

    private function contactUs()
    {

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