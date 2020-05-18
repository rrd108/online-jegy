<?php
require('./headers.php');
require('./secrets.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = file_get_contents('php://input');

    if (isset(json_decode($data)->email) && isset(json_decode($data)->password)) {
        // with this identification passwords should be unique
        if(json_decode($data)->email == array_search(md5(json_decode($data)->password), $secrets['users'])) {
            echo md5(json_decode($data)->email . json_decode($data)->password);
            return;
        }
        return;
    }
}