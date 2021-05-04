<?php

/*
 * Its a virtual javascript file to add internal all scripts 
 */
global $RWR;

$customScript = isset($_SESSION['publicScripts']) ? $_SESSION['publicScripts'] : "";

//unset($_SESSION['publicScripts']);

$rqGet = $RWR->get();
if (isset($rqGet['public.js'])) {
    header('Content-Type: application/javascript');
    ksort($customScript);
    //var_dump($customScript);
    //Event: load/ready & function base
    $functionBaseScript = "";
    foreach ($customScript as $scPart) {
        if (is_array($scPart)) {
            continue;
        }
        $functionBaseScript.="$scPart\n";
    }

    $script = "";
    $readyEvent = "
$(document).ready(function(){ ";
    if (isset($customScript['ready'])) {
        ksort($customScript['ready']);
        foreach ($customScript['ready'] as $readyScript) {
            $readyEvent.=$readyScript;
        }
    }
    $readyEvent.="
});";

    $loadEvent = "
$(window).on('load',function(){ ";
    if (isset($customScript['load'])) {
        ksort($customScript['load']);
        foreach ($customScript['load'] as $readyScript) {
            $loadEvent.=$readyScript;
        }
    }
    $loadEvent.= "
});
";

    $script = $readyEvent . $loadEvent . $functionBaseScript;
    echo $script;
    exit;
}