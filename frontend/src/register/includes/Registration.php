<?php
namespace nba\src\register\includes;
//require(__DIR__ . "/../lib/nav.php");
require(__DIR__. "/../../lib/sanitizers.php");
abstract class Registration {

    /**
    * Displays main login page.
    * @return void
    */
    public static function displayRegistration() {
        self::handleRegistration();
?>

    <!DOCTYPE html>
    <html lang='en'>

        <head>
            <?php echo \nba\src\lib\components\Head::displayHead(); ?> 
        </head>

        <body>
        <div class="w-full max-w-md"> 
        <div class="relative md:flex md:items-start mb-6">
            <form id="registerForm" method="POST">
            <div class="md:flex md:items-start mb-6">
                        <label class="items-start block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="email">Email Address</label>
                        <input class="appearance-none border-4 border-gray-500 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" id="email" type="text" placeholder="Jane@test.com" required>
                    </div>
                    <div class="md:flex md:items-start mb-6">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="pw">Password</label>
                        <input class="appearance-none border-4 border-gray-500 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" type="password" id="pw" name="password" required minlength="8" />
                    </div>
                    <div class="md:flex md:items-start mb-6">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="confirmPassword">Confirm Password</label>
                        <input class="appearance-none border-4 border-gray-500 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" type="password" name="confirmPassword" required minlength="8" />
                    </div>
                    <div class="md:flex md:items-center mb-6">
                        <input type="submit" value="Register" />
                    </div>
            </div>
            </form>
            
            <div id="statusMessage"></div>
            <div class="w-full max-w-md"> 
                <div class=" relative md:flex items-start">
                    <h2 class="text-xl font-bold mx-10 px-10">Already have an account?</h2>
    </div>
    <div class=" relative md:flex items-start">
                    <a class="mx-10 px-10" href="../../login/"> Sign In</a>
                </div>
        </body>
    </html>

    <?php
    } //end of displayRegistration()

    private static function handleRegistration() {
        try{
            if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirmPassword"])) {
                $email = filter_input(INPUT_POST,'email');
                $password = filter_input(INPUT_POST,'password');
                $confirm = filter_input(INPUT_POST,'confirmPassword');
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
                    echo "empty password";
                    $hasError = true;
                }
                if (empty($confirm)) {
                    echo ("empty confirm");
                    $hasError = true;
                }
                if (!is_valid_password($password)) {
                    echo ("invalid pass");
                    $hasError = true;
                }
                if ((strlen($password) > 0) && ($password !== $confirm)) {
                    echo ("password and confirm must match");
                    $hasError = true;
                }
                if (!$hasError) {
                    //$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $salt = '$2y$10$';

                    // Hash the password with the specified salt using bcrypt
                    $hashedPassword = $password;
                    //echo $password;
                    //echo $hashedPassword;
        
                    $json_message = json_encode(['type'=>'register_request', 'email' => $email, 'password' => $hashedPassword]);
                    $client = new \nba\rabbit\RabbitMQClient(__DIR__.'/../../../rabbit/host.ini', "Authentication");
                    //print_r($json_message);
                    if($client->send_request($json_message, 'register_request')) {
                    //echo "Message published successfuly:  $json_message";
                    } else {
                    echo "Failed to publish message: $json_message";
                    }
                }
            } 
        } catch (\Exception $e){
            echo ('Error processing registration'.$e->getMessage());
        }
    }
}