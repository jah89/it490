<?php
namespace nba\src\admin\includes;

/**
 * Class that handles login attempts.
 * Validates/sanitizes user inputs before passing it to login 
 * function of SessionHandler class.
 */
abstract class Admin {

    private false|\nba\shared\Session $session;

    
    /**
    * Displays main login page.
    * @return void
    */
    public static function displayAdminPage() {
        ?>

    <!DOCTYPE html>
    <html lang='en'>

        <head>
            <?php echo \nba\src\lib\components\Head::displayHead(); 
            echo \nba\src\lib\components\Nav::displayNav(); 
            $session = \nba\src\lib\SessionHandler::getSession();
        error_log("session" . $session . print_r($session, true));
        if(!$session){
            error_log("Admin paged access without valid session data");
            header('Location: /login');
            exit();
        }
        else {
            error_log('Checking user for admin status');
            /*checks admin status here*/
            $request = ['type' => 'admin_check_request', 'email' => $session->getEmail()];
            $rabbitClient = new \nba\rabbit\RabbitMQClient(__DIR__.'/../../../rabbit/host.ini', "Authentication");
            $response = $rabbitClient->send_request(json_encode($request), 'application/json');
            $responseData = json_decode($response, true);
            $isAdmin = $responseData['result'];
            if($isAdmin != true){
                header('Location: /home');
                exit();
            } else{

        ?>
        </head>

        <body>

            <h1 class="text-xl lg:text-4xl">hello commissioner</h1>
            <h2 class="text-lg lg:text-xl">Users to manage goes here</h2>
                <div id="statusMessage"></div>
        </body>
    </html>

    <?php
    }
    } //end of displayAdminPage()
}
}
