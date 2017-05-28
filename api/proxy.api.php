<?php

include_once(dirname(__DIR__) . '\classes\proxy\ProxyController.php');

error_reporting(E_ERROR);

try {
    if (isset($_GET['action']) && !empty($_GET['action'])) {
        $controller = new ProxyController();
        $controller = $controller->{$_GET['action']}();
    } else {
        throw new CustomException("Action not specified", 400);
    }
} catch (CustomException $e) {
    $e->handle();
}