<?php
namespace nba\src\login\includes;
include(__DIR__.'/../../lib/sanitizers.php');

/**
 * Class that handles login attempts.
 * Validates/sanitizes user inputs before passing it to login 
 * function of SessionHandler class.
 */
abstract class Login {

    private static false|\nba\shared\Session $session;

    private static function handleLogin() {
        try{
            if (isset($_POST["email"]) && isset($_POST["password"])) {
                $email = filter_input(INPUT_POST,'email');
                $password = filter_input(INPUT_POST,'password');

                $hasError = false;
                
                if (empty($email)) {
                    $hasError = true;
                }
                
                //sanitize
                $email = sanitize_email($email);
                //validate
                if (!is_valid_email($email)) {
                    echo ("Bad email");
                    $hasError = true;
                }
                if (empty($password)) {
                    echo "Bad password";
                    $hasError = true;
                }
                if (!is_valid_password($password)) {
                    echo ("invalid pass");
                    $hasError = true;
                }

                if (!$hasError) {
                    //$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $salt = '$2y$10$';

                    // Hash the password with the specified salt using bcrypt
                    $hashedPassword = crypt($password, $salt);
                    static::$session = \nba\src\lib\SessionHandler::login($email, $hashedPassword);
                    ?>
                    <?php

                    if(static::$session) {
                        header('Location: /home');
                        exit();
                    } else {

                        die("Failed to process login request.");

                    }
                } 
            }
        }catch(\Exception $e){
            echo("Error processing login ".$e->getMessage());
        }
    }//end handleLogin()

    /**
    * Displays main login page.
    * @return void
    */
    public static function displayLogin() {

        self::handleLogin();
        ?>

    <!DOCTYPE html>
    <html lang='en'>

        <head>
            <?php echo \nba\src\lib\components\Head::displayHead(); ?> 
        </head>

        <body>
            <h1 class="text-3xl font-bold underline">hello there sports fans, how is your day today?</h1>

            <form  id="loginForm" method="POST">
                <div>
                    <label for="email">Email Address</label>
                    <input type="text" name="email" required />
                </div>
                <div>
                    <label for="pw">Password</label>
                    <input type="password" id="pw" name="password" required minlength="8" />
                </div>
                
                <input type="submit" value="Login" />
            </form>
            <div id="statusMessage"></div>

            <h2 class="text-xl font-bold">Don't have an account?</h2>
            <a href="../../register/"> Sign Up</a>
        </body>
    </html>

    <?php
    } //end of displayLogin()
    
}