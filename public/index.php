<?php

require_once "../app/controllers/BarangController.php";

$controller = new BarangController();

$action = $_GET['action'] ?? 'index';

switch ($action) {

    case 'register':
        $controller->register();
        break;
        
    case 'login':
        $controller->login();
        break;

    case 'logout':
        $controller->logout();
        break;

    case 'create':
        $controller->create();
        break;

    case 'store':
        $controller->store();
        break;

    case 'edit':
        $controller->edit();
        break;

    case 'update':
        $controller->update();
        break;

    case 'delete':
        $controller->destroy();
        break;

    default:
        $controller->index();
        break;
}