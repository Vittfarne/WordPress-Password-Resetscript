<?php
/*
 * @author: Fredrik Vittfarne (http://fidde.nu)
 * @created: 1 October 2014
 * @updated: 8 October 2014
 * Tested with Wordpress 4.
 */

//Check if a language selection has been made.
if (isset($_GET['lang'])){
    switch ($_GET['lang']){
        case 'en':
            $lng = 'en';
            break;
        case 'sv':
            $lng = 'sv';
            break;
        default:
            $lng = 'en';
            break;
    }
} else {
    //If no language has been selected, choose English as the default.
    $lng = 'en';
}
 $LANG = [
        /*
         * Swedish is the default language to display the page in, simply add ?lang='newlang' to change.
         * Example ?lang=en for english
         * To translate, simply add a new array for the new language with the same language keys as
         * English or Swedish.
        */
         'en' => [
             'dbname'        =>  'Database name',
             'title'         =>  'Wordpress password resetting script',
             'dbuser'        =>  'Database user',
             'dbhost'        =>  'Database host',
             'dbpassword'    =>  'Database password',
             'submit'        =>  'Go',
             'reset'         =>  'Clear',
             'tblprefix'     =>  'Table prefix',
             'newusrn'       =>  'Username for new user',
             'newpasswd'     =>  'Password for new user',
             'plaintext'     =>  'Plain text',
             'language'      =>  'Language',
             'formsub'       =>  'Form submitted ...',
             'dbfail'        =>  'Database connection failed ...',
             'dbtest'        =>  'Testing connection to database ... ',
             'dbcons'        =>  'Database connection established  ...',
             'cmpl'         =>  'User inserted and given administrator privileges. You can now login.<br>Script complete.
        <br> Script created by Fredrik Vittfarne (<a href="http://fidde.nu" target="_blank">http://fidde.nu</a>).<br>
        This is avaible @ <a href="http://github.com/vittfarne/WordPress-Password-Resetscript" target="_blank">http://github.com/vittfarne/WordPress-Password-Resetscript</a>',
             'langnames'     =>   [
                 'en'    =>  'English',
                 'sv'    =>  'Swedish'
             ]
         ],
        'sv' => [
            'dbname'        =>  'Databasnamn',
            'title'         =>  'Lösenordsåterställning för Wordpress',
            'dbuser'        =>  'Databasanvändare',
            'dbhost'        =>  'Databasvärd',
            'dbpassword'    =>  'Databaslösenord',
            'submit'        =>  'Kör',
            'reset'         =>  'Töm',
            'tblprefix'     =>  'Tabellprefix',
            'newusrn'       =>  'Användarnamn för ny användare',
            'newpasswd'     =>  'Lösenord för ny användare',
            'plaintext'     =>  'Klartext',
            'language'      =>  'Språk',
            'formsub'       =>  'Formuläret skickat ...',
            'dbfail'        =>  'Databasanslutningen misslyckades ...',
            'dbtest'        =>  'Testar databasanslutningen ... ',
            'dbcons'        =>  'Databasanslutningen lyckades ...',
            'cmpl'         =>  'User inserted and given administrator privileges. You can now login.<br>Script complete.
    <br> Script created by Fredrik Vittfarne (<a href="http://fidde.nu" target="_blank">http://fidde.nu</a>).<br>
    This is avaible @ <a href="http://github.com/vittfarne/WordPress-Password-Resetscript" target="_blank">http://github.com/vittfarne/WordPress-Password-Resetscript</a>',
            'langnames'     =>   [
                    'en'    =>  'Engelska',
                    'sv'    =>  'Svenska'
                ]
            ]
        ];

?>
<!doctype html>
<html lang="<?php echo $lng; ?>">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $LANG[$lng]['title']; ?></title>
        <style>
            tr {
                width: auto;
            }
            td {
                width: auto;
            }
        </style>
        <script>
            /*
             * @author Fredrik Vittfarne (http://fidde.nu)
             * Simple Javascript to switch between visible and hidden password fields
             */
            function togglePW ($fieldID, $checkboxID){
                var CheckboxInfo = document.getElementById($checkboxID);
                if(CheckboxInfo.checked){
                    document.getElementById($fieldID).type="text";
                }else{
                    document.getElementById($fieldID).type="password";
                }
            }
        </script>
    </head>
    <body>
        <form action="" method="get" onchange="submit()" onselect="submit()">
            <label for="lang"><?php echo $LANG[$lng]['language'];?>:</label>
            <select name="lang" id="lang">
        <?php
        foreach ($LANG as $key => $value){
            if ($lng === $key) {$arg = "selected";} else {$arg = "";}
            echo "
                <option $arg value=\"$key\">
                    ".$LANG[$lng]['langnames'][$key]."
                </option>
                ";
        }
        ?>

            </select>
        </form>
        <?php

        if (!empty($_POST)){
            echo $LANG[$lng]['formsub'] . " <br>";

            $info = [
                'db' => [
                    'host'      =>  $_POST['dbhost'],
                    'user'      =>  $_POST['dbuser'],
                    'pass'      =>  $_POST['dbpassword'],
                    'name'      =>  $_POST['dbname'],
                    'tblprefix' =>  $_POST['tblprefix']
                ],
                'user' => [
                    'username'  =>  $_POST['newusrn'],
                    'password'  =>  md5($_POST['newpasswd'])
            ]];

            echo $LANG[$lng]['dbtest'] . " <br>";

            try {
                $pdo = new PDO('mysql:host=' . $info['db']['host'] . ';charset=utf8;dbname=' . $info['db']['name'], $info['db']['user'], $info['db']['pass']);
                $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            } catch(PDOException $e) {
                echo $LANG[$lng]['dbfail'] . " <br>";
                die ($e->getMessage());
            }
            echo $LANG[$lng]['dbcons'] . " <br>";

            $pdo->beginTransaction();

            $usertable = $info['db']['tblprefix']."users";

            $sql = "INSERT INTO $usertable (user_login, user_pass) VALUES (:username, :password);";
            $sth = $pdo->prepare($sql);

            $sth->bindParam(':username', $info['user']['username']);
            $sth->bindParam(':password', $info['user']['password']);

            $sth->execute();

            $insertid = $pdo->lastInsertId();

            $metatable = $info['db']['tblprefix'].'usermeta';
            $sql2 = "INSERT INTO $metatable (user_id, meta_key, meta_value) VALUES (:userid, :meta_key, 'a:1:{s:13:\"administrator\";b:1;}');";
            $sth2 = $pdo->prepare($sql2);
            $sth2->bindValue(':meta_key', $info['db']['tblprefix'].'capabilities');
            $sth2->bindParam(':userid', $insertid);
            $sth2->execute();

            $pdo->commit();
            echo $LANG[$lng]['cmpl'];
        }


        ?>

        <form action="" method="post">
            <table>
                <tr>
                    <td>
                        <label for="dbhost"><?php echo $LANG[$lng]['dbhost']; ?>:</label>
                    </td>
                    <td>
                        <input type="text" name="dbhost" id="dbhost" placeholder="<?php echo $LANG[$lng]['dbhost']; ?>" required/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="dbname"><?php echo $LANG[$lng]['dbname']; ?>:</label>
                    </td>
                    <td>
                        <input type="text" name="dbname" id="dbname" placeholder="<?php echo $LANG[$lng]['dbname']; ?>" required/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="dbuser"><?php echo $LANG[$lng]['dbuser']; ?>:</label>
                    </td>
                    <td>
                        <input type="text" name="dbuser" id="dbuser" placeholder="<?php echo $LANG[$lng]['dbuser']; ?>" required/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="dbpassword"><?php echo $LANG[$lng]['dbpassword']; ?>:</label>
                    </td>
                    <td>
                        <input type="text" name="dbpassword" id="dbpassword" placeholder="<?php echo $LANG[$lng]['dbpassword']; ?>" />
                        <input type="checkbox" id="dbpasswordtoggle" onclick="togglePW('dbpassword', 'dbpasswordtoggle');"/>
                        <label for="dbpasswordtoggle"><?php echo $LANG[$lng]['plaintext']; ?></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="tblprefix"><?php echo $LANG[$lng]['tblprefix']; ?>:</label>
                    </td>
                    <td>
                        <input type="text" name="tblprefix" id="tblprefix" placeholder="<?php echo $LANG[$lng]['tblprefix']; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="newusrn"><?php echo $LANG[$lng]['newusrn']; ?>:</label>
                    </td>
                    <td>
                        <input type="text" name="newusrn" id="newusrn" placeholder="<?php echo $LANG[$lng]['newusrn']; ?>" required/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="newpasswd"><?php echo $LANG[$lng]['newpasswd']; ?>:</label>
                    </td>
                    <td>
                        <input type="password" name="newpasswd" id="newpasswd" placeholder="<?php echo $LANG[$lng]['newpasswd']; ?>" required/>
                        <input type="checkbox" id="newpasswdtoggle" onclick="togglePW('newpasswd', 'newpasswdtoggle');"/>
                        <label for="newpasswdtoggle"><?php echo $LANG[$lng]['plaintext']; ?></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" value="<?php echo $LANG[$lng]['submit']; ?>"/>
                    </td>
                    <td>
                        <input type="reset" value="<?php echo $LANG[$lng]['reset']; ?>"/>
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
