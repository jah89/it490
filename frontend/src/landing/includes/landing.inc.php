<?php

//frontend main landing page
namespace NBA\Frontend\Src\Landing\Includes;
require __DIR__.'/../../../vendor/autoload.php';
use NBA\Frontend\Src\Lib\Components\Head;

abstract class Landing {

    /**
    * Displays main landing page.
    * @return void
    */
    public static function displayLanding() {

        ?>
        <html>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            
           <?php 
           //TO DO fix this
           //$head = new Head();
           //echo $head->displayHead();
           var_dump(class_exists('NBA\\Frontend\\Src\\Lib\\Compnents\\Head')); ?> 
</head>

        <body>
            <h1 class="text-3xl font-bold underline">hello there sports fans, how is your day today?</h1>
            <?php 
            //TO DO: make components for Nav, header, and footer.  
            //TO DO: Make session logic.
            ?>
            <a href="../login"> Login Here</a>
        </body>
        </html>
    <?php

    } //end of displayLanding()
}
?>