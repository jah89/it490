<?php

//frontend main landing page
namespace NBA\Frontend\src\landing\includes;
//use NBA\Frontend\Src\Lib\Components\Head;

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
           //echo \NBA\Frontend\Lib\Components\Head::displayHead();
           //var_dump(class_exists('NBA\\Frontend\\Src\\Lib\\Components\\Head'));
           //var_dump(class_exists('NBA\\Frontend\\Src\\Landing\\Includes\\Landing')); ?> 

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