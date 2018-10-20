<?php

class PlexLogin {

    private static $loginEndpoint = "https://plex.tv/users/sign_in.xml";
    private static $serversEndpoint = "https://plex.tv/pms/servers.xml?X-Plex-Token=";

    private $username = null;
    private $password = null;

    private $authToken = null;

    private $clientId = null;

    private $properties = array(
        'deviceName' => 'PlexLogin',
        'product' => 'PlexLogin',
        'version' => '0.3',
        'platform' => array(
            'name' => 'PlexLogin',
            'version' => '0.3'
        ),
        'clientIdentifier' => 'PlexLogin/0.3'
    );

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function isLoggedIn() {
        return ($this->authToken != null);
    }

    public function login() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::$loginEndpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/xml; charset=utf-8',
            'X-Plex-Device-Name: ' . $this->properties['deviceName'],
            'X-Plex-Product: ' . $this->properties['product'],
            'X-Plex-Version: ' . $this->properties['version'],
            'X-Plex-Platform: ' . $this->properties['platform']['name'],
            'X-Plex-Platform-Version: ' . $this->properties['platform']['version'],
            'X-Plex-Client-Identifier: ' . $this->properties['clientIdentifier'],
            'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password)
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        $xml = simplexml_load_string($server_output);

        if ($xml === false)
            return false;

        if ($xml['authenticationToken']) {
            $this->username = sprintf("%s", $xml['username']);
            $this->email = sprintf("%s", $xml['email']);
            $this->authToken = sprintf("%s", $xml['authenticationToken']);

            return true;
        }

        return false;
    }

    public function getUsername() {
        if (!$this->isLoggedIn())
            return false;

        return $this->username;
    }

    public function getEmail() {
        if (!$this->isLoggedIn())
            return false;

        return $this->email;
    }

    public function getProperties() {
        return $properties;
    }

    public function setProperties($properties) {
        $this->properties = $properties;
    }

    public function setProperty($key, $value) {
        $this->properties[$key] = $value;
    }

    public function getServers() {
        if (!$this->isLoggedIn())
            return [];

        $arr = [];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::$serversEndpoint . $this->authToken);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        $xml = simplexml_load_string($server_output);

        if ($xml === false)
            return false;

        foreach ($xml as $media_container) {
            $arr[] = array(
                'name' => sprintf("%s", $media_container['name']),
                'address' => sprintf("%s", $media_container['address']),
                'identifier' => sprintf("%s", $media_container['machineIdentifier']),
                'owned' => sprintf("%s", $media_container['owned'])
            );
        }

        return $arr;
    }

}

?>
