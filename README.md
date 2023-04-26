PlexLogin
=========

A simple PHP class that allows you to authenticate against the Plex.tv servers using a username/password and check the servers associated with that account.

Usage example:

    <?php

    require_once('PlexLogin.class.php');

    $pl = new PlexLogin('username', 'password', 'totp');

    if ($pl->login()) {
      print_r($pl->getServers());
    } else {
      echo 'Login failed.';
    }
    
    ?>
