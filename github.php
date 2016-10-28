<?php

class GitHub
{
    private $API = 'https://api.github.com/';
    private $REPOS = 'repos/';
    private $LATEST = '/releases/latest';

    private $name;
    private $repo;
	
	private $clientid;
	private $clientsecret;

    public function __construct($name, $repo, $clientid, $clientsecret)
    {
        ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0)');
        $this->name = $name;
        $this->repo = '/'.$repo;
		$this->clientid = $clientid;
		$this->clientsecret = $clientsecret;
    }

    private function getLatestRelease()
    {
        return json_decode(file_get_contents($this->API.$this->REPOS.$this->name.$this->repo.$this->LATEST.'?client_id='.$this->clientid.'&client_secret='.$this->clientsecret), true);
    }

    public function getLatestVersion()
    {
        return $this->getLatestRelease()['name'];
    }
}

?>
