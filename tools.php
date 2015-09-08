<?php

if ($_GET["action"] == "version_compare")
{
  print '{"result": "'.version_compare($_GET["check"], $_GET["latest"]).'"}';
}

?>
