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
        return $this->makeCurl("sendMessage", ["chat_id" => $this->user_id, "text" => $text, "reply_markup" => json_encode([
            "inline_keyboard" =>

                $inline

        ])]);
    }

    private function sendPhoto($url)
    {
        return $this->makeCurl("sendPhoto", ["chat_id" => $this->user_id, "photo" => $url]);
    }

    private function editMessageText($text, $inline)
    {
        return $this->makeCurl("editMessageText", ["message_id" => $this->message_id ,"chat_id" => $this->user_id, "text" => $text, "reply_markup" => json_encode([
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

    private function afterEmailText()
    {

        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.users WHERE user_id = {$this->user_id}");
        $row = mysqli_fetch_array($result);
        mysqli_query($this->db, "UPDATE sahlan_bot.users SET email = '{$this->text}', email_status = NULL WHERE user_id = {$this->user_id}");
        $this->text = $row['email_status'];

    }

    public function process()
    {
        if ($this->emailStatus != NULL)
            $this->afterEmailText();
        if ($this->text == "/start")
            $this->starter();
        elseif ($this->text == "Contact_Us")
            $this->contactUs();
        elseif ($this->level == "begin")
            $this->beginner();
        elseif ($this->level == "product_showed")
            $this->productManager();
        elseif ($this->level == "office_partition_showed")
            $this->officePartitionManager();
        elseif ($this->level == "classic_partition_showed")
            $this->classicPartitionManager();
        elseif ($this->level == "frimls_partition_showed")
            $this->frimlsPartitionManager();
        elseif ($this->level == "office_couch_showed")
            $this->officeCouchManager();
        elseif ($this->level == "lima_couch_showed")
            $this->limaCouchManager();
        elseif ($this->level == "timber_couch_showed")
            $this->timberCouchManager();
        elseif ($this->level == "edar_couch_showed")
            $this->edarCouchManager();
        elseif ($this->level == "karin_couch_showed")
            $this->karinCouchManager();
        elseif ($this->level == "group_desk_showed")
            $this->groupDeskManager();
        elseif ($this->level == "project_showed")
            $this->projectManager();
        elseif ($this->level == "office_project_showed")
            $this->officeProjectManager();
        elseif ($this->level == "exhibition_project_showed")
            $this->exhibitionProjectManager();
        elseif ($this->level == "office_supply_showed")
            $this->officeSupplyManager();
        elseif ($this->level == "exhibition_structure_showed")
            $this->exhibitionStructureManager();
        elseif ($this->level == "all_office_project_showed")
            $this->allOfficeProjectManager();
        elseif ($this->level == "best_office_project_showed")
            $this->bestOfficeProjectManager();
        elseif ($this->level == "best_exhibition_project_showed")
            $this->bestExhibitionProjectManager();
        elseif ($this->level == "all_exhibition_project_showed")
            $this->allExhibitionProjectManager();

    }


    private function frimlsPartitionManager()
    {
        if ($this->text == "Get_Catalog")
        {
            $this->editMessageText("فایل را دریافت کنید", []);
            $this->sendDocument($this->getFrimlsPartitionCatalog());
            $this->sendMessage("برای بازگشت بر روی منوی اصلی بزنید.", [
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
        elseif ($this->text == "Contact_Us")
            $this->contactUs();
        elseif ($this->text == "Main_Menu")
        {
            $this->setLevel("begin");
            $this->showMainMenu(true);
        }
        elseif ($this->text == "Related_Projects")
            $this->showBestOfficeProject();
    }

    private function setEmailStatus($status)
    {
        mysqli_query($this->db, "UPDATE sahlan_bot.users SET email_status = '{$status}'");
    }

    private function getEmailStatus()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.users WHERE user_id = {$this->user_id}");

        if ($row = mysqli_fetch_array($result))
            return $row['email_status'];
        else
            return NULL;
    }

    private function emailGetting()
    {
        $this->setEmailStatus($this->text);
        $this->editMessageText("برای دریافت کاتالوگ ایمیل خود را وارد کنید.", []);
    }

    private function classicPartitionManager()
    {

            if ($this->text == "Get_Catalog")
            {
                $this->editMessageText("فایل را دریافت کنید", []);
                $this->sendDocument($this->getClassicPartitionCatalog());
                $this->sendMessage("برای بازگشت بر روی منوی اصلی بزنید.", [
                    [
                        ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                    ]
                ]);
            }
            elseif ($this->text == "Contact_Us")
                $this->contactUs();
            elseif ($this->text == "Main_Menu")
            {
                $this->setLevel("begin");
                $this->showMainMenu(true);
            }
            elseif ($this->text == "Related_Projects")
                $this->showBestOfficeProject();
    }

    private function getClassicPartitionCatalog()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.catalog");
        $row = mysqli_fetch_array($result);
        return $row['classic_partition'];
    }

    private function getFrimlsPartitionCatalog()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.catalog");
        $row = mysqli_fetch_array($result);
        return $row['frimls_partition'];
    }

    private function getLimaCouchCatalog()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.catalog");
        $row = mysqli_fetch_array($result);
        return $row['lima_couch'];
    }

    private function getTimberCouchCatalog()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.catalog");
        $row = mysqli_fetch_array($result);
        return $row['timber_couch'];
    }

    private function getKarinCouchCatalog()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.catalog");
        $row = mysqli_fetch_array($result);
        return $row['karin_couch'];
    }

    private function getEdarCouchCatalog()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.catalog");
        $row = mysqli_fetch_array($result);
        return $row['edar_couch'];
    }

    private function getGroupDeskCatalog()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.catalog");
        $row = mysqli_fetch_array($result);
        return $row['group_desk'];
    }

    private function getOfficeSupplytCatalog()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.catalog");
        $row = mysqli_fetch_array($result);
        return $row['esa'];
    }

    private function getExhibitionStructureCatalog()
    {
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.catalog");
        $row = mysqli_fetch_array($result);
        return $row['exhibition'];
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
        $result = mysqli_query($this->db, "SELECT * FROM sahlan_bot.users WHERE user_id = {$this->user_id}");
        $row = mysqli_fetch_array($result);
        if ($row['email'])
        {
            return true;
        }
        else
            return false;
    }

    private function officePartitionManager()
    {
        if ($this->text == "Classic_Partition")
            $this->showClassicPartition();
        elseif ($this->text == "Partition_Frimls")
            $this->showFrimlsPartition();
    }

    private function showMainMenu($editStatus)
    {
        $text = "با استفاده از منوی زیر، می توانید از محصولات و پروژه های ما دیدن فرمایید.";
        $inline_keyboard =
            [
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
                    ["text" => "تماس با سهلان", "callback_data" => "Contact_Us"]
                ]
            ];
        if ($editStatus == true)
            $this->editMessageText($text, $inline_keyboard);
        else
            $this->sendMessage($text, $inline_keyboard);
    }

    private function showFrimlsPartition()
    {
        if (!$this->checkMail())
            $this->emailGetting();
        else
        {
            $this->setLevel("frimls_partition_showed");
            $this->sendMessage("پارتیشن اداري یکی از فاکتورهاي مهم در طراحی فضاهاي اداري است. محیط کاري شما باید پاسخگوي نیازهاي شما باشد، باید
خود را با نیازهاي شما تطبیق دهد و همچنان نقش خود را به عنوان عاملی در آسایش و جذب نیروهاي کاري ایفا کند.", [
                [
                    ["text" => "دریافت کاتالوگ", "callback_data" => "Get_Catalog"]
                ],
                [
                    ["text" => "برخی از پروژه ها", "callback_data" => "Related_Projects"]
                ],
                [
                    ["text" => "تماس با ما", "callback_data" => "Contact_Us"]
                ],
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
    }

    private function showClassicPartition()
    {
        if (!$this->checkMail())
            $this->emailGetting();
        else {
            $this->setLevel("classic_partition_showed");
            $this->sendMessage("ارتیشن کلاسیک سهلان به گونه طراحی شده است که دفتر کاري شما در ذهن ها ماندگار شود. طراحی هوشمند و تطابق با
جدیدترین متدهاي طراحی فضاي اداري، اصلی ترین ویژگی هاي پارتیشن کلاسیک محسوب می شوند", [
                [
                    ["text" => "دریافت کاتالوگ", "callback_data" => "Get_Catalog"]
                ],
                [
                    ["text" => "برخی از پروژه ها", "callback_data" => "Related_Projects"]
                ],
                [
                    ["text" => "تماس با ما", "callback_data" => "Contact_Us"]
                ],
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
    }

    private function officeCouchManager()
    {
        if ($this->text == "Lima_Couch")
            $this->showLimaCouch();
        elseif ($this->text == "Timber_Couch")
            $this->showTimberCouch();
        elseif ($this->text == "Edar_Couch")
            $this->showEdarCouch();
        elseif ($this->text == "Karin_Couch")
            $this->showKarinCouch();
        elseif ($this->text == "Group_Desk")
            $this->showGroupDesk();
    }

    private function showLimaCouch()
    {
        $this->setLevel("lima_couch_showed");
        $this->editMessageText("میز اداری و مبلمان اداری لیما آمیزه ای از چوب و فلز در نهایت ظرافت و با تمرکز بر ترکیب های چینشی متفاوت برای محیط های کاری با چیدمان های متفاوت طراحی شده اند.",[
            [
                ["text" => "دریافت کاتالوگ", "callback_data" => "Get_Catalog"]
            ],
            [
                ["text" => "برخی از پروژه ها", "callback_data" => "Related_Projects"]
            ],
            [
                ["text" => "تماس با ما", "callback_data" => "Contact_Us"]
            ],
            [
                ["text" => "صفحه ی اصلی", "callback_data" => "Main_Menu"]
            ]
        ]);
    }

    private function limaCouchManager()
    {
        if ($this->text == "Get_Catalog")
        {
            $this->editMessageText("فایل را دریافت کنید", []);
            $this->sendDocument($this->getLimaCouchCatalog());
            $this->sendMessage("برای بازگشت بر روی منوی اصلی بزنید.", [
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
        elseif ($this->text == "Contact_Us")
            $this->contactUs();
        elseif ($this->text == "Main_Menu")
        {
            $this->setLevel("begin");
            $this->showMainMenu(true);
        }
        elseif ($this->text == "Related_Projects")
            $this->showBestOfficeProject();
    }

    private function showTimberCouch()
    {
        $this->setLevel("timber_couch_showed");
        $this->editMessageText("میز مدیریت تیمبر با طراحی مدرن و با جزئیات بی عیب و نقص خود یکی از گزینه های اصلی مدیران و کارشناسان می باشد.",[
            [
                ["text" => "دریافت کاتالوگ", "callback_data" => "Get_Catalog"]
            ],
            [
                ["text" => "برخی از پروژه ها", "callback_data" => "Related_Projects"]
            ],
            [
                ["text" => "تماس با ما", "callback_data" => "Contact_Us"]
            ],
            [
                ["text" => "صفحه ی اصلی", "callback_data" => "Main_Menu"]
            ]
        ]);
    }

    private function timberCouchManager()
    {
        if ($this->text == "Get_Catalog")
        {
            $this->editMessageText("فایل را دریافت کنید", []);
            $this->sendDocument($this->getTimberCouchCatalog());
            $this->sendMessage("برای بازگشت بر روی منوی اصلی بزنید.", [
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
        elseif ($this->text == "Contact_Us")
            $this->contactUs();
        elseif ($this->text == "Main_Menu")
        {
            $this->setLevel("begin");
            $this->showMainMenu(true);
        }
        elseif ($this->text == "Related_Projects")
            $this->showBestOfficeProject();
    }

    private function showEdarCouch()
    {
        $this->setLevel("edar_couch_showed");
        $this->editMessageText("راهی زیبا، حریم شخصی مشخص، ملحقات متنوع به همراه امکان چینش های متفاوت، میزهای ادار را به عنوان گزینه ای مناسب برای مبلمان اداری معرفی نموده است.",[
            [
                ["text" => "دریافت کاتالوگ", "callback_data" => "Get_Catalog"]
            ],
            [
                ["text" => "برخی از پروژه ها", "callback_data" => "Related_Projects"]
            ],
            [
                ["text" => "تماس با ما", "callback_data" => "Contact_Us"]
            ],
            [
                ["text" => "صفحه ی اصلی", "callback_data" => "Main_Menu"]
            ]
        ]);
    }

    private function edarCouchManager()
    {
        if ($this->text == "Get_Catalog")
        {
            $this->editMessageText("فایل را دریافت کنید", []);
            $this->sendDocument($this->getEdarCouchCatalog());
            $this->sendMessage("برای بازگشت بر روی منوی اصلی بزنید.", [
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
        elseif ($this->text == "Contact_Us")
            $this->contactUs();
        elseif ($this->text == "Main_Menu")
        {
            $this->setLevel("begin");
            $this->showMainMenu(true);
        }
        elseif ($this->text == "Related_Projects")
            $this->showBestOfficeProject();
    }

    private function showKarinCouch()
    {
        $this->setLevel("karin_couch_showed");
        $this->editMessageText("میزهای کاربین سهلان با استفاده از دیوایدرها و ماژول های مختلف فضای اختصاصی را برای پرسنل فراهم می نماید و بطور همزمان امکان ارتباط بین پرسنل مختلف را به راحتی میسر می سازند",[
            [
                ["text" => "دریافت کاتالوگ", "callback_data" => "Get_Catalog"]
            ],
            [
                ["text" => "برخی از پروژه ها", "callback_data" => "Related_Projects"]
            ],
            [
                ["text" => "تماس با ما", "callback_data" => "Contact_Us"]
            ],
            [
                ["text" => "صفحه ی اصلی", "callback_data" => "Main_Menu"]
            ]
        ]);
    }

    private function karinCouchManager()
    {
        if ($this->text == "Get_Catalog")
        {
            $this->editMessageText("فایل را دریافت کنید", []);
            $this->sendDocument($this->getKarinCouchCatalog());
            $this->sendMessage("برای بازگشت بر روی منوی اصلی بزنید.", [
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
        elseif ($this->text == "Contact_Us")
            $this->contactUs();
        elseif ($this->text == "Main_Menu")
        {
            $this->setLevel("begin");
            $this->showMainMenu(true);
        }
        elseif ($this->text == "Related_Projects")
            $this->showBestOfficeProject();
    }

    private function showGroupDesk()
    {
        $this->setLevel("group_desk_showed");
        $this->editMessageText("با استفاده از میز گروهی سهلان می توانید همزمان فضای کاری شخصی و فضای کار تیمی داشته باشید.",[
            [
                ["text" => "دریافت کاتالوگ", "callback_data" => "Get_Catalog"]
            ],
            [
                ["text" => "برخی از پروژه ها", "callback_data" => "Related_Projects"]
            ],
            [
                ["text" => "تماس با ما", "callback_data" => "Contact_Us"]
            ],
            [
                ["text" => "صفحه ی اصلی", "callback_data" => "Main_Menu"]
            ]
        ]);
    }

    private function groupDeskManager()
    {
        if ($this->text == "Get_Catalog")
        {
            $this->editMessageText("فایل را دریافت کنید", []);
            $this->sendDocument($this->getGroupDeskCatalog());
            $this->sendMessage("برای بازگشت بر روی منوی اصلی بزنید.", [
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
        elseif ($this->text == "Contact_Us")
            $this->contactUs();
        elseif ($this->text == "Main_Menu")
        {
            $this->setLevel("begin");
            $this->showMainMenu(true);
        }
        elseif ($this->text == "Related_Projects")
            $this->showBestOfficeProject();
    }

    private function beginner()
    {
        if ($this->text == "Product_Us_Button")
            $this->showProduct();
        elseif ($this->text == "Project_Us_Button")
            $this->showProject();
        elseif ($this->text == "About_Us_Button")
            $this->showAbout();
        elseif ($this->text == "Main_Menu")
            $this->showMainMenu(true);

    }

    private function productManager()
    {
        if ($this->text == "Office_Partition")
            $this->showOfficePartition();
        elseif ($this->text == "Office_Couch")
            $this->showOfficeCouch();
        elseif ($this->text == "Office_Supply")
            $this->showOfficeSupply();
        elseif ($this->text == "Exhibition_Structure")
            $this->showExhibitionStructure();
    }

    private function showOfficeSupply()
    {
        if (!$this->checkMail())
            $this->emailGetting();
        else {
            $this->setLevel("office_supply_showed");
            $this->sendMessage("اهمیت محیط کار و امکانات و لوازم جانبی اداری موجود در آن بر کسی پوشیده نیست. بدون شک امکانات موجود در محیط کار سبب افزایش بازدهی پرسنل سازمان می شود. ما این اهمیت را درک کرده ایم و تلاش خود را برای رفع نیازهای یک سازمان برای تجهیز فضای اداری خود بکار گرفته ایم.", [
                [
                    ["text" => "دریافت کاتالوگ", "callback_data" => "Get_Catalog"]
                ],
                [
                    ["text" => "برخی از پروژه ها", "callback_data" => "Related_Projects"]
                ],
                [
                    ["text" => "تماس با ما", "callback_data" => "Contact_Us"]
                ],
                [
                    ["text" => "صفحه ی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
    }

    private function officeSupplyManager()
    {
        if ($this->text == "Get_Catalog")
        {
            $this->editMessageText("فایل را دریافت کنید", []);
            $this->sendDocument($this->getOfficeSupplytCatalog());
            $this->sendMessage("برای بازگشت بر روی منوی اصلی بزنید.", [
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
        elseif ($this->text == "Contact_Us")
            $this->contactUs();
        elseif ($this->text == "Main_Menu")
        {
            $this->setLevel("begin");
            $this->showMainMenu(true);
        }
        elseif ($this->text == "Related_Projects")
            $this->showBestOfficeProject();
    }

    private function showExhibitionStructure()
    {
        if (!$this->checkMail())
            $this->emailGetting();
        else {
            $this->setLevel("exhibition_structure_showed");
            $this->sendMessage("گروه سهلان با دید ساده سازی فرآیند حضور سازمان ها در نمایشگاه ها و کاهش استرس های ناشی از فراآیند ساخت غرفه نمایشگاهی با تکیه بر دانش و تکنولوژی خود سازه ای خاص و نوین با حداقل زمان مورد نیاز برای پیاده سازی را طراحی و تولید نموده است.", [
                [
                    ["text" => "دریافت کاتالوگ", "callback_data" => "Get_Catalog"]
                ],
                [
                    ["text" => "برخی از پروژه ها", "callback_data" => "Related_Projects"]
                ],
                [
                    ["text" => "تماس با ما", "callback_data" => "Contact_Us"]
                ],
                [
                    ["text" => "صفحه ی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
    }

    private function exhibitionStructureManager()
    {
        if ($this->text == "Get_Catalog")
        {
            $this->editMessageText("فایل را دریافت کنید", []);
            $this->sendDocument($this->getExhibitionStructureCatalog());
            $this->sendMessage("برای بازگشت بر روی منوی اصلی بزنید.", [
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
        elseif ($this->text == "Contact_Us")
            $this->contactUs();
        elseif ($this->text == "Main_Menu")
        {
            $this->setLevel("begin");
            $this->showMainMenu(true);
        }
        elseif ($this->text == "Related_Projects")
            $this->showBestExhibitionProject();
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

    private function showOfficeCouch()
    {
        if (!$this->checkMail())
            $this->emailGetting();
        else {
            $this->setLevel("office_couch_showed");
            $this->sendMessage("محصولات سهلان به دو دسته کلی دکوراسیون اداري و سازه هاي نمایشگاهی تقسیم می شود.
شما می توانید با استفاده از منوي زیر کاتالوگ هاي هر محصول را دریافت کنید و پروژه هاي مربوط به آن را مشاهده نمایید.", [
                [
                    ["text" => "مبلمان اداري لیما", "callback_data" => "Lima_Couch"]
                ],
                [
                    ["text" => "مبلمان اداري تیمبر", "callback_data" => "Timber_Couch"]
                ],
                [
                    ["text" => "مبلمان اداري ادار", "callback_data" => "Edar_Couch"]
                ],
                [
                    ["text" => "مبلمان اداري کارین", "callback_data" => "Karin_Couch"]
                ],
                [
                    ["text" => "میزهاي گروهی", "callback_data" => "Group_Desk"]
                ]
            ]);
        }
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

    private function getProjects($type, $best)
    {
        if ($best == 1)
            return mysqli_query($this->db, "SELECT * FROM sahlan_bot.project WHERE type = '{$type}'AND best = 1");
        elseif ($best == 0)
            return mysqli_query($this->db, "SELECT * FROM sahlan_bot.project WHERE type = '{$type}'");
    }

    private function projectPageNumber($type, $best)
    {
        if ($best == 0)
            $count = mysqli_query($this->db, "SELECT COUNT(*) FROM sahlan_bot.project WHERE type = '{$type}'");
        else
            $count = mysqli_query($this->db, "SELECT COUNT(*) FROM sahlan_bot.project WHERE type = '{$type}' AND best = 1");
        $row = mysqli_fetch_array($count);
        $count = $row[0];

        if (is_int($count/5))
            return $count/5;
        else
            return floor($count/5) + 1;
    }

    private function getDate($name)
    {
        return mysqli_query($this->db, "SELECT * FROM sahlan_bot.project WHERE name_english = '{$name}'");
    }

    private function showProject()
    {
        if (!$this->checkMail())
            $this->emailGetting();
        else
        {
            $this->setLevel("project_showed");
            $this->sendMessage("گروه صنعتی سهلان با در اختیار داشتن سه کارخانه مجهز در منطقه خرمدشت تهران و با بهره مندی از 14000 متر مربع فضای تولیدی و قدرت تولید سالانه 27000 متر مربع پارتیشن، توانایی پاسخ گویی به نیازهای طیف وسیعی از سازمان ها را در صنعت پارتیشن و مبلمان اداری دارد. ظرفیت تولید بالا و تنوع محصولات، سهلان را به یکی از بزرگترین فعالان صنعت دکوراسیون اداری تبدیل کرده است.",[
                [
                    ["text" => "پروژه های اداری", "callback_data" => "Office_Project"]
                ],
                [
                    ["text" => "پروژه های نمایشگاهی", "callback_data" => "Exhibition_Project"]
                ]
            ]);
        }
    }

    private function projectManager()
    {
        if ($this->text == "Office_Project")
            $this->showOfficeProject();
        elseif ($this->text== "Exhibition_Project")
            $this->showExhibitionProject();
    }

    private function showOfficeProject()
    {
        $this->setLevel("office_project_showed");
        $this->editMessageText("با تکیه بر توانایی تولیدی، و با استفاده از به روزترین ماشین آلات و با کیفیت ترین مواد اولیه، تجهیز فضای اداری بسیاری از شرکت های داخلی و خارجی را در ایران برعهده داشته ایم. اعتماد و رضایت به دست آمده از اجرای این طرح ها را تأییدی بر صحت مسیر خود می دانیم.", [
            [
                ["text" => "پروژه های برتر", "callback_data" => "Best_Projects"]
            ],
            [
                ["text" => "تمامی پروژه ها", "callback_data" => "All_Projects"]
            ]
        ]);
    }

    private function officeProjectManager()
    {
        if ($this->text == "Best_Projects")
            $this->showBestOfficeProject();
        elseif ($this->text == "All_Projects")
            $this->showAllOfficeProject();

    }

    private function showBestOfficeProject()
    {
        if (!$this->checkMail())
            $this->emailGetting();
        else {
            $this->setLevel("best_office_project_showed");
            $result = $this->getProjects("office", 1);
            $count = 0;
            $arr = [[["text" => "منوی اصلی", "callback_data" => "Main_Menu"]]];
            while ($row = mysqli_fetch_array($result))
            {
                array_push($arr, [["text" => $row['name'], "callback_data" => $row['name_english']]]);
                $count++;
                if ($count > 4)
                    break;
            }
            if ($this->projectPageNumber("office", 1) > 1)
                array_push($arr, [["text" => "صفحه ی بعد", "callback_data" => "Next_Page_1"]]);
            $this->sendMessage("انتخاب کنید.",$arr);
        }
    }

    private function bestOfficeProjectManager()
    {
        if ($this->text == "Main_Menu")
        {
            $this->setLevel("begin");
            $this->showMainMenu(true);
        }
        elseif ($this->text[0] == "N" && $this->text[1] == "e" && $this->text[2] == "x")
        {
            $result = $this->getProjects("office", 1);
            $arr = [[["text" => "منوی اصلی", "callback_data" => "Main_Menu"]]];
            $current_page = (int)substr($this->text, 10, strlen($this->text) - 10);
            for ($i = 1 ; $i <  $current_page + 1; $i++)
            {
                $count = 0;
                while ($row = mysqli_fetch_array($result))
                {
                    $count++;
                    if ($count > 4)
                        break;
                }
            }
            $count = 0;
            $result_temp = $result;
            while ($row = mysqli_fetch_array($result_temp))
            {
                array_push($arr,[["text" => $row['name'], "callback_data" => $row['name_english']]] );
                $count++;
                if ($count > 4)
                    break;
            }

            $pageNumber = $this->projectPageNumber("office", 1);
            $nextPage = $current_page + 1;
            if (($current_page + 1)  == $pageNumber)
            {
                array_push($arr, [["text" => "صفحه ی قبل", "callback_data" => "Previews_Page_{$nextPage}"]]);
            }
            else
            {
                array_push($arr, [["text" => "صفحه ی قبل", "callback_data" => "Previews_Page_{$nextPage}"], ["text" => "صفحه ی بعد", "callback_data" => "Next_Page_{$nextPage}"]]);
            }

            $this->editMessageText("انتخاب کنید.", $arr);

        }
        elseif ($this->text[0] == "P" && $this->text[1] == "r" && $this->text[2] == "e")
        {
            $result = $this->getProjects("office", 1);
            $arr = [[["text" => "منوی اصلی", "callback_data" => "Main_Menu"]]];
            $current_page = (int)substr($this->text, 14, strlen($this->text) - 14);
            for ($i = 1 ; $i <  $current_page - 1; $i++)
            {
                $count = 0;
                while ($row = mysqli_fetch_array($result))
                {
                    $count++;
                    if ($count > 4)
                        break;
                }
            }
            $count = 0;
            $result_temp = $result;
            while ($row = mysqli_fetch_array($result_temp))
            {
                array_push($arr,[["text" => $row['name'], "callback_data" => $row['name_english']]] );
                $count++;
                if ($count > 4)
                    break;
            }
            if (($current_page - 1) == 1)
            {
                array_push($arr, [["text" => "صفحه ی بعد", "callback_data" => "Next_Page_1"]]);
            }
            else
            {
                $prePage = $current_page - 1;
                array_push($arr, [["text" => "صفحه ی قبل", "callback_data" => "Previews_Page_{$prePage}"],["text" => "صفحه ی بعد", "callback_data" => "Next_Page_{$prePage}"]]);
            }
            $this->editMessageText("انتخاب کنید.", $arr);

        }
        elseif($row = mysqli_fetch_array($this->getDate($this->text)))
        {
            $urls = explode("*",$row['urls']);
            for ($i = 0 ; $i < count($urls) ; $i++)
            {
                $this->sendPhoto($urls[$i]);
            }
            $this->sendMessage("بازگشت به منوی اصلی", [
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
        else
        {
            $this->setLevel("begin");
            $this->showMainMenu(false);
        }
    }

    private function showAllOfficeProject()
    {
        if (!$this->checkMail())
            $this->emailGetting();
        else {
            $this->setLevel("all_office_project_showed");
            $result = $this->getProjects("office", 0);
            $count = 0;
            $arr = [[["text" => "منوی اصلی", "callback_data" => "Main_Menu"]]];
            while ($row = mysqli_fetch_array($result))
            {
                array_push($arr, [["text" => $row['name'], "callback_data" => $row['name_english']]]);
                $count++;
                if ($count > 4)
                    break;
            }
            if ($this->projectPageNumber("office", 0) > 1)
                array_push($arr, [["text" => "صفحه ی بعد", "callback_data" => "Next_Page_1"]]);
            $this->sendMessage("انتخاب کنید.",$arr);
        }

    }

    private function allOfficeProjectManager()
    {
        if ($this->text == "Main_Menu")
        {
            $this->setLevel("begin");
            $this->showMainMenu(true);
        }
        elseif ($this->text[0] == "N" && $this->text[1] == "e" && $this->text[2] == "x")
        {
            $result = $this->getProjects("office", 0);
            $arr = [[["text" => "منوی اصلی", "callback_data" => "Main_Menu"]]];
            $current_page = (int)substr($this->text, 10, strlen($this->text) - 10);
            for ($i = 1 ; $i <  $current_page + 1; $i++)
            {
                $count = 0;
                while ($row = mysqli_fetch_array($result))
                {
                    $count++;
                    if ($count > 4)
                        break;
                }
            }
            $count = 0;
            $result_temp = $result;
            while ($row = mysqli_fetch_array($result_temp))
            {
                array_push($arr,[["text" => $row['name'], "callback_data" => $row['name_english']]] );
                $count++;
                if ($count > 4)
                    break;
            }

            $pageNumber = $this->projectPageNumber("office", 0);
            $nextPage = $current_page + 1;
            if (($current_page + 1)  == $pageNumber)
            {
                array_push($arr, [["text" => "صفحه ی قبل", "callback_data" => "Previews_Page_{$nextPage}"]]);
            }
            else
            {
                array_push($arr, [["text" => "صفحه ی قبل", "callback_data" => "Previews_Page_{$nextPage}"], ["text" => "صفحه ی بعد", "callback_data" => "Next_Page_{$nextPage}"]]);
            }

            $this->editMessageText("انتخاب کنید.", $arr);

        }
        elseif ($this->text[0] == "P" && $this->text[1] == "r" && $this->text[2] == "e")
        {
            $result = $this->getProjects("office", 0);
            $arr = [[["text" => "منوی اصلی", "callback_data" => "Main_Menu"]]];
            $current_page = (int)substr($this->text, 14, strlen($this->text) - 14);
            for ($i = 1 ; $i <  $current_page - 1; $i++)
            {
                $count = 0;
                while ($row = mysqli_fetch_array($result))
                {
                    $count++;
                    if ($count > 4)
                        break;
                }
            }
            $count = 0;
            $result_temp = $result;
            while ($row = mysqli_fetch_array($result_temp))
            {
                array_push($arr,[["text" => $row['name'], "callback_data" => $row['name_english']]] );
                $count++;
                if ($count > 4)
                    break;
            }
            if (($current_page - 1) == 1)
            {
                array_push($arr, [["text" => "صفحه ی بعد", "callback_data" => "Next_Page_1"]]);
            }
            else
            {
                $prePage = $current_page - 1;
                array_push($arr, [["text" => "صفحه ی قبل", "callback_data" => "Previews_Page_{$prePage}"],["text" => "صفحه ی بعد", "callback_data" => "Next_Page_{$prePage}"]]);
            }
            $this->editMessageText("انتخاب کنید.", $arr);

        }
        elseif($row = mysqli_fetch_array($this->getDate($this->text)))
        {
            $urls = explode("*",$row['urls']);
            for ($i = 0 ; $i < count($urls) ; $i++)
            {
                $this->sendPhoto($urls[$i]);
            }
            $this->sendMessage("بازگشت به منوی اصلی", [
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
        else
        {
            $this->setLevel("begin");
            $this->showMainMenu(false);
        }
    }

    private function showExhibitionProject()
    {
        $this->setLevel("exhibition_project_showed");
        $this->editMessageText("با تکیه بر توانایی تولیدی، و با استفاده از به روزترین ماشین آلات و با کیفیت ترین مواد اولیه، تجهیز فضای اداری بسیاری از شرکت های داخلی و خارجی را در ایران برعهده داشته ایم. اعتماد و رضایت به دست آمده از اجرای این طرح ها را تأییدی بر صحت مسیر خود می دانیم.", [
            [
                ["text" => "پروژه های برتر", "callback_data" => "Best_Projects"]
            ],
            [
                ["text" => "تمامی پروژه ها", "callback_data" => "All_Projects"]
            ]
        ]);
    }

    private function exhibitionProjectManager()
    {
        if ($this->text == "Best_Projects")
            $this->showBestExhibitionProject();
        elseif ($this->text == "All_Projects")
            $this->showAllExhibitionProject();
    }

    private function showBestExhibitionProject()
    {
        if (!$this->checkMail())
            $this->emailGetting();
        else {
            $this->setLevel("best_exhibition_project_showed");
            $result = $this->getProjects("ex", 1);
            $count = 0;
            $arr = [[["text" => "منوی اصلی", "callback_data" => "Main_Menu"]]];
            while ($row = mysqli_fetch_array($result))
            {
                array_push($arr, [["text" => $row['name'], "callback_data" => $row['name_english']]]);
                $count++;
                if ($count > 4)
                    break;
            }
            if ($this->projectPageNumber("ex", 1) > 1)
                array_push($arr, [["text" => "صفحه ی بعد", "callback_data" => "Next_Page_1"]]);
            $this->sendMessage("انتخاب کنید.",$arr);
        }
    }

    private function bestExhibitionProjectManager()
    {
        if ($this->text == "Main_Menu")
        {
            $this->setLevel("begin");
            $this->showMainMenu(true);
        }
        elseif ($this->text[0] == "N" && $this->text[1] == "e" && $this->text[2] == "x")
        {
            $result = $this->getProjects("ex", 1);
            $arr = [[["text" => "منوی اصلی", "callback_data" => "Main_Menu"]]];
            $current_page = (int)substr($this->text, 10, strlen($this->text) - 10);
            for ($i = 1 ; $i <  $current_page + 1; $i++)
            {
                $count = 0;
                while ($row = mysqli_fetch_array($result))
                {
                    $count++;
                    if ($count > 4)
                        break;
                }
            }
            $count = 0;
            $result_temp = $result;
            while ($row = mysqli_fetch_array($result_temp))
            {
                array_push($arr,[["text" => $row['name'], "callback_data" => $row['name_english']]] );
                $count++;
                if ($count > 4)
                    break;
            }

            $pageNumber = $this->projectPageNumber("ex", 1);
            $nextPage = $current_page + 1;
            if (($current_page + 1)  == $pageNumber)
            {
                array_push($arr, [["text" => "صفحه ی قبل", "callback_data" => "Previews_Page_{$nextPage}"]]);
            }
            else
            {
                array_push($arr, [["text" => "صفحه ی قبل", "callback_data" => "Previews_Page_{$nextPage}"], ["text" => "صفحه ی بعد", "callback_data" => "Next_Page_{$nextPage}"]]);
            }

            $this->editMessageText("انتخاب کنید.", $arr);

        }
        elseif ($this->text[0] == "P" && $this->text[1] == "r" && $this->text[2] == "e")
        {
            $result = $this->getProjects("ex", 1);
            $arr = [[["text" => "منوی اصلی", "callback_data" => "Main_Menu"]]];
            $current_page = (int)substr($this->text, 14, strlen($this->text) - 14);
            for ($i = 1 ; $i <  $current_page - 1; $i++)
            {
                $count = 0;
                while ($row = mysqli_fetch_array($result))
                {
                    $count++;
                    if ($count > 4)
                        break;
                }
            }
            $count = 0;
            $result_temp = $result;
            while ($row = mysqli_fetch_array($result_temp))
            {
                array_push($arr,[["text" => $row['name'], "callback_data" => $row['name_english']]] );
                $count++;
                if ($count > 4)
                    break;
            }
            if (($current_page - 1) == 1)
            {
                array_push($arr, [["text" => "صفحه ی بعد", "callback_data" => "Next_Page_1"]]);
            }
            else
            {
                $prePage = $current_page - 1;
                array_push($arr, [["text" => "صفحه ی قبل", "callback_data" => "Previews_Page_{$prePage}"],["text" => "صفحه ی بعد", "callback_data" => "Next_Page_{$prePage}"]]);
            }
            $this->editMessageText("انتخاب کنید.", $arr);

        }
        elseif($row = mysqli_fetch_array($this->getDate($this->text)))
        {
            $urls = explode("*",$row['urls']);
            for ($i = 0 ; $i < count($urls) ; $i++)
            {
                $this->sendPhoto($urls[$i]);
            }
            $this->sendMessage("بازگشت به منوی اصلی", [
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
        else
        {
            $this->setLevel("begin");
            $this->showMainMenu(false);
        }
    }

    private function showAllExhibitionProject()
    {
        if (!$this->checkMail())
            $this->emailGetting();
        else {
            $this->setLevel("all_exhibition_project_showed");
            $result = $this->getProjects("ex", 0);
            $count = 0;
            $arr = [[["text" => "منوی اصلی", "callback_data" => "Main_Menu"]]];
            while ($row = mysqli_fetch_array($result))
            {
                array_push($arr, [["text" => $row['name'], "callback_data" => $row['name_english']]]);
                $count++;
                if ($count > 4)
                    break;
            }
            if ($this->projectPageNumber("ex", 0) > 1)
                array_push($arr, [["text" => "صفحه ی بعد", "callback_data" => "Next_Page_1"]]);
            $this->sendMessage("انتخاب کنید.",$arr);
        }
    }

    private function allExhibitionProjectManager()
    {
        if ($this->text == "Main_Menu")
        {
            $this->setLevel("begin");
            $this->showMainMenu(true);
        }
        elseif ($this->text[0] == "N" && $this->text[1] == "e" && $this->text[2] == "x")
        {
            $result = $this->getProjects("ex", 0);
            $arr = [[["text" => "منوی اصلی", "callback_data" => "Main_Menu"]]];
            $current_page = (int)substr($this->text, 10, strlen($this->text) - 10);
            for ($i = 1 ; $i <  $current_page + 1; $i++)
            {
                $count = 0;
                while ($row = mysqli_fetch_array($result))
                {
                    $count++;
                    if ($count > 4)
                        break;
                }
            }
            $count = 0;
            $result_temp = $result;
            while ($row = mysqli_fetch_array($result_temp))
            {
                array_push($arr,[["text" => $row['name'], "callback_data" => $row['name_english']]] );
                $count++;
                if ($count > 4)
                    break;
            }

            $pageNumber = $this->projectPageNumber("ex", 0);
            $nextPage = $current_page + 1;
            if (($current_page + 1)  == $pageNumber)
            {
                array_push($arr, [["text" => "صفحه ی قبل", "callback_data" => "Previews_Page_{$nextPage}"]]);
            }
            else
            {
                array_push($arr, [["text" => "صفحه ی قبل", "callback_data" => "Previews_Page_{$nextPage}"], ["text" => "صفحه ی بعد", "callback_data" => "Next_Page_{$nextPage}"]]);
            }

            $this->editMessageText("انتخاب کنید.", $arr);

        }
        elseif ($this->text[0] == "P" && $this->text[1] == "r" && $this->text[2] == "e")
        {
            $result = $this->getProjects("ex", 0);
            $arr = [[["text" => "منوی اصلی", "callback_data" => "Main_Menu"]]];
            $current_page = (int)substr($this->text, 14, strlen($this->text) - 14);
            for ($i = 1 ; $i <  $current_page - 1; $i++)
            {
                $count = 0;
                while ($row = mysqli_fetch_array($result))
                {
                    $count++;
                    if ($count > 4)
                        break;
                }
            }
            $count = 0;
            $result_temp = $result;
            while ($row = mysqli_fetch_array($result_temp))
            {
                array_push($arr,[["text" => $row['name'], "callback_data" => $row['name_english']]] );
                $count++;
                if ($count > 4)
                    break;
            }
            if (($current_page - 1) == 1)
            {
                array_push($arr, [["text" => "صفحه ی بعد", "callback_data" => "Next_Page_1"]]);
            }
            else
            {
                $prePage = $current_page - 1;
                array_push($arr, [["text" => "صفحه ی قبل", "callback_data" => "Previews_Page_{$prePage}"],["text" => "صفحه ی بعد", "callback_data" => "Next_Page_{$prePage}"]]);
            }
            $this->editMessageText("انتخاب کنید.", $arr);

        }
        elseif($row = mysqli_fetch_array($this->getDate($this->text)))
        {
            $urls = explode("*",$row['urls']);
            for ($i = 0 ; $i < count($urls) ; $i++)
            {
                $this->sendPhoto($urls[$i]);
            }
            $this->sendMessage("بازگشت به منوی اصلی", [
                [
                    ["text" => "منوی اصلی", "callback_data" => "Main_Menu"]
                ]
            ]);
        }
        else
        {
            $this->setLevel("begin");
            $this->showMainMenu(false);
        }
    }

    private function showAbout()
    {
        $this->editMessageText("بیش از 25  سال است که در سهلان با هدف ارتقا سطح کیفی محیط های کاری سازمان های کوچک و بزرگ، فعالیت خود را آغاز نموده ایم. برای دستیابی به این هدف، تلاش می کنیم تا محیط های کاری را مطابق با نیازهای امروز تعریف، طراحی و تجهیز نماییم. باور داریم محیط کاری و فضای اداری یک سازمان نقش مهمی در پیشرفت کسب وکار دارد و سبب افزایش اعتماد به نفس و انگیزه پرسنل سازمان می شود.",[[["text" => "بازگشت", "callback_data" => "Main_Menu"]]]);
    }

    private function contactUs()
    {
        $this->editMessageText("با استفاده از شماره زیر کارشناسان ما آماده راهنمایی شما هستند:
02142890000
 همچنین می توانید از طریق وبسایت ما از جدیدترین محصولا و پروژه های ما مطلع شوید:
www.sahlan.co", [
    [
        ["text" => "بازگشت", "callback_data" => "Main_Menu"]
    ]
        ]);
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
                ["text" => "تماس با سهلان", "callback_data" => "Contact_Us"]
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