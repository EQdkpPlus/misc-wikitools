<?php

$result = "false";

switch ($_GET["action"])
{
        case "compare_version":
                if (strpos($_GET["sample"], "x") !== false)
                {
                        if (version_compare(str_replace("x", "0", $_GET["sample"]), $_GET["latest"]) != 1 && version_compare(str_replace("x", "999", $_GET["sample"]), $_GET["latest"]) != -1)
                        {
                                $result = 0;
                        }
                        elseif (version_compare(str_replace("x", "999", $_GET["sample"]), $_GET["latest"]) == 1)
                        {
                                $result = 1;
                        }
                        elseif (version_compare(str_replace("x", "0", $_GET["sample"]), $_GET["latest"]) == -1)
                        {
                                $result = -1;
                        }
                }
                else
                {
                        $result = version_compare($_GET["sample"], $_GET["latest"]);
                }
                break;
}

print '{"result": "'.$result.'"}';
 
?>
