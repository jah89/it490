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
                    //$salt = '$2y$10$';

                    // Hash the password with the specified salt using bcrypt
                    $hashedPassword = $password;
                    static::$session = \nba\src\lib\SessionHandler::login($email, $hashedPassword);
                    ?>
                    <?php
                    //error_log('hi' . print_r(static::$session, true));
                    if(static::$session == false){
                        echo("Login attempt failed, please try again.");
                    } else {

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

            <h1 class= "mb-6 lg:text-3xl font-bold lg:mb-10">hello there sports fans, how is your day today?</h1>
            <div class="w-full max-w-md"> 
                <div class="relative md:flex md:items-center mb-6">

                <form  class="w-full max-w-sm" id="loginForm" method="POST">
                <div class="md:flex md:items-center mb-6">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="email">Email Address</label>
                        <input class="appearance-none border-4 border-gray-500 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" id="email" type="text" placeholder="Jane@test.com" required>
                    </div>
                    <div class="md:flex md:items-center mb-6">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="pw">Password</label>
                        <input class="appearance-none border-4 border-gray-500 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" type="password" id="pw" name="password" required minlength="8" />
                    </div>
                    <div class="md:flex md:items-center mb-6">
                        <input type="submit" value="Login" />
                    </div>
                </form>
                <div id="statusMessage"></div>
            </div>
            <div class="w-full max-w-md"> 
                <div class=" relative md:flex items-center">
                    <h2 class="text-xl font-bold mx-10 px-10">Don't have an account?</h2>
    </div>
    <div class=" relative md:flex items-center">
                    <a class="mx-10 px-10" href="../../register/"> Sign Up</a>
                </div>
            </div>
        </body>
    </html>

    <?php
    } //end of displayLogin()
    
}