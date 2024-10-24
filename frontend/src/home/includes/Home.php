<?php

namespace nba\src\home\includes;

abstract class Home {

    private false|\nba\shared\Session $session;

    /**
    * Displays user's homepage.
    * @return void
    */
    public static function displayHome() {

?>

    <!DOCTYPE html>
    <html lang='en'>

        <head>
        <?php 
        echo \nba\src\lib\components\Head::displayHead();
        echo \nba\src\lib\components\Nav::displayNav();
        $session = \nba\src\lib\SessionHandler::getSession();
        error_log("session" . $session . print_r($session, true));
        if(!$session){
            header('Location: /login');
            exit();
        } else {
            $fullEmail = htmlspecialchars($session->getEmail(), ENT_QUOTES, 'UTF-8');
            $atPos = strpos($fullEmail, '@');
            if ($atPos !== false) {
                $uname = substr($fullEmail, 0, $atPos);
            } else {
                $uname = $fullEmail;
            }
        }
        ?>
        <script>
        // Pass session data to JavaScript
        window.sessionUser = {
            uname: <?php echo json_encode($uname);?> 
        }
    </script>
        </head>


        <body>
        <div class="relative flex min-h-screen flex-col 
        justify-center overflow-hidden bg-slate-200 px-12 py-6 sm:py-32 lg:py-14">
            <h1 class="text-lg lg:text-2xl font-bold">HOME</h1>
            <div class="text-lg lg:text-3xl space-y-6 py-8 leading-7 text-gray-600">
            <table class="table-auto">
                <thead>

                    <tr >
                    <th class="px-12"></th>
                    <th class="px-12 font-extrabold underline underline-offset-3">Player Stats</th>
                    <th class="px-12"></th>    
                    </tr>
                    <tr>
                    <th class="px-12">Name</th>
                    <th class="px-12">wins/losses?</th>
                    <th class="px-12">shooting pct?</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            </div>
        </div>
            <?php
            require __DIR__.'/../../lib/chat/chatFront.php';
            ?>
            
            <a href="../../logout/" class="hover:text-3xl pb-20"> Logout</a>
        </body>
    </html>
    <script src="chat.js"></script>
    <?php
    } //end of displayLogin()
    
}