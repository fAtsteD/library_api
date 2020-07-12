<?php

require_once '../vendor/autoload.php';

use App\App;

try {
    $app = new App();
    echo $app->run();
} catch (Exception $e) {
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage(), 'code' => $e->getCode()));
}
