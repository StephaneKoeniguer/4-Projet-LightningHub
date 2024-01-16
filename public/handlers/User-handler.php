<?php
require_once __DIR__.'/../../bootstrap/app.php';

if (!empty($_POST['action'])) {

    if ($_POST['action'] === 'store') {
        $controller = new App\Controllers\UserController();
        $controller->store();
    } elseif ($_POST['action'] === 'login') {
        $controller = new App\Controllers\AuthController();
        $controller->login();
    } elseif ($_POST['action'] === 'logout') {
        $controller = new App\Controllers\AuthController();
        $controller->logout();
    } elseif ($_POST['action'] === 'deleteaccount') {
        $controller = new App\Controllers\UserController();
        $controller->delete();
    }elseif ($_POST['action'] === 'picture') {
        $controller = new App\Controllers\UserController();
        $controller->savePicture();
    }elseif ($_POST['action'] === 'idGame') {
        \App\Controllers\PlayGamesController::updateInsGameUserName(intval($_GET['idUser']), intval($_GET['idGame']));
    }elseif ($_POST['action'] === 'updatemail') {
        \App\Controllers\UserController::updateEmail(intval($_GET['idUser']));
    }elseif ($_POST['action'] === 'updateusername') {
        \App\Controllers\UserController::updateUsername(intval($_GET['idUser']));
    }elseif ($_POST['action'] === 'update_notification') {
        //todo: $_POST
        \App\Controllers\UserController::updateNotification(intval($_GET['idUser']));
    }elseif ($_POST['action'] === 'sendmail') {
        // todo: Connection smtp
        Mail::sendMail();
    }

}

