<?php

switch ($_GET["action"])
{
        case "compare_version":
                $result = version_compare($_GET["sample"], $_GET["latest"]);
                break;
        case "contains_placeholder":
                $result = "false";
                if (strpos($_GET["sample"], "x"))
                {
                        $result = "true";
                }
                break;
}

print '{"result": "'.$result.'"}';

?>
