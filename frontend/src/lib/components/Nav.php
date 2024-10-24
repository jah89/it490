<?php
namespace nba\src\lib\components;

/**
 * Navigation bar.
 */
abstract class Nav
{


    /**
     * Echoes navigation bar.
     *
     * @return void
     */
    public static function displayNav()
    {
        ?>
    <nav class="navbar navbar-expand-lg navbar-light">
      <div class="container-fluid">
        <a class="navbar-brand" href="/landing">NBA FANTASY</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link active" href="/landing">Main</a>
            </li>
            <?php
            $session = \nba\src\lib\SessionHandler::getSession();
            if ($session) {
                ?>
            <li>
              <a class="nav-link active" href="/draft">Draft</a>
            </li>
            <li>
              <a class="nav-link active" href="/players">Player Stats</a>
            </li>
            <li>
              <a class="nav-link active" href="/myteam">Team Management</a>
            </li>
            <?php } ?>
            <li class="nav-item dropdown account-nav">
              <a class="nav-link active dropdown-toggle"
              id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown"> 
              <?php
                if ($session) {
                    echo htmlspecialchars($session->getEmail());
                } else {
                    ?>
                  Account 
                <?php } ?></a>
              <ul class="dropdown-menu">
              <?php
                if ($session) {
                    ?>
                  <li><a class="dropdown-item" href="/logout">Logout</a></li>
                    <?php
                } else {
                    ?>
                  <li><a class="dropdown-item" href="/login">Login</a></li>
                  <li><a class="dropdown-item" href="/register">Register</a></li>
                <?php } ?>
              </ul>
            </li class="nav-item">
          </ul>
        </div>
      </div>
    </nav>
        <?php

    }//end displayNav()


}//end class