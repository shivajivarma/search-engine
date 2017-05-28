<?php

include_once(dirname(__DIR__) . '\classes\indexer\IndexerController.php');
include_once(dirname(__DIR__) . '\classes\CustomException.php');

error_reporting(E_ERROR);

try {
    if (isset($_GET['action']) && !empty($_GET['action'])) {
        $controller = new IndexerController();
        $controller = $controller->{$_GET['action']}();
    } else {
        throw new CustomException("Action not specified", 400);
    }
} catch (CustomException $e) {
    $e->handle();
}