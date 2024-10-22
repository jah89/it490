<?php

namespace nba\src\draft\includes;

abstract class Draft {
    /**
    * Displays user's homepage.
    * @return void
    */
    public static function displayDraft() {

?>

    <!DOCTYPE html>
    <html lang='en'>

        <head>
        <?php echo \nba\src\lib\components\Head::displayHead();
            $session = \nba\src\lib\SessionHandler::getSession();
            if(!$session){
                header('Location: /login');
                exit();
            } else {
                $fullEmail = htmlspecialchars($session->getEmail(), ENT_QUOTES, 'UTF-8');
                $endUname = strlen($fullEmail)- (strpos($fullEmail, '@'));
                $uname =   substr($fullEmail, 0, $endUname);
            }
            ?>
            <script>
            // Pass session data to JavaScript
            const sessionUser = {
                uname: <?php echo($uname);?> 
            };
        </script>
        </head>
        <body></body>
        <?php
        }
    }
    ?>