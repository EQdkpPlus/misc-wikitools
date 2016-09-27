<?php

class GitHub
{
    private $API = 'https://api.github.com/';
    private $REPOS = 'repos/';
    private $LATEST = '/releases/latest';

    private $name;
    private $repo;

    public function __construct($name, $repo)
    {
        ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0)');
        $this->name = $name;
        $this->repo = '/'.$repo;
    }

    private function getLatestRelease()
    {
        return json_decode(file_get_contents($this->API.$this->REPOS.$this->name.$this->repo.$this->LATEST), true);
    }

    public function getLatestVersion()
    {
        return $this->getLatestRelease()['name'];
    }
}

?>
