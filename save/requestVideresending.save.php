<?php

use UKMNorge\Arrangement\Videresending\Request\RequestVideresending;
use UKMNorge\Arrangement\Videresending\Request\Write as WriteRequestVideresending;

$reqVideresendingObj = null;
$success = false;

if($_POST['arrangement_fra'] && $_POST['arrangement_til']) {
    $reqVideresending = new RequestVideresending(-1, intval($_POST['arrangement_fra']), intval($_POST['arrangement_til']), '', false);
    $resultObj = WriteRequestVideresending::createOrUpdate($reqVideresending);
    
    if($resultObj != null && $resultObj->getId() != -1) {
        $success = true;
    }
}

UKMVideresending::addViewData(
    [
        'success' => $success
    ]
);