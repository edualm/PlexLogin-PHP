PlexLogin
=========

A simple PHP class that allows you to authenticate against the Plex.tv servers using a username/password and check the servers associated with that account.

Usage example:

    $pl = new PlexLogin('username', 'password');
  
    if ($pl->login()) {
      print_r($pl->getServers());
    } else {
      echo 'Login failed.'
    }
