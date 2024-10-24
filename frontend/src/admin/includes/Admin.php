<?php
namespace nba\src\admin\includes;

/**
 * Class that handles login attempts.
 * Validates/sanitizes user inputs before passing it to login 
 * function of SessionHandler class.
 */
abstract class Admin {

    private static false|\nba\shared\Session $session;

    
    /**
    * Displays main login page.
    * @return void
    */
    public static function displayAdminPage() {
        ?>

    <!DOCTYPE html>
    <html lang='en'>

        <head>
            <?php echo \nba\src\lib\components\Head::displayHead(); ?> 
        </head>

        <body>

            <h1 class="text-xl lg:text-4xl">hello commissioner</h1>
            <h2 class="text-lg lg:text-xl">Users to manage goes here</h2>
                <div id="statusMessage"></div>
        </body>
    </html>

    <?php
    } //end of displayLogin()
    
}