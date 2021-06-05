<?php
use PDO;

// TODO do not responde to api calls without authentication check if we have a valid token

require('./headers.php');
require('./secrets.php');
require('./simplepay/config.php');
require('./simplepay/SimplePayV21.php');

$prices = [
    'adult' => 5840,
    'child' => 4840,
    'herbs' => 12000
];

$maxSlots = [
    'tematic' => 30,
    'herbs' => 15
];

// no programs on these days
$fileSpecialDays = './specialDays';
$handle = fopen($fileSpecialDays, "r");
$specialDays = explode("\n", fread($handle, filesize($fileSpecialDays)));
fclose($handle);

$pdo = new PDO('mysql:host=localhost;dbname=' . $secrets['mysqlTable'], $secrets['mysqlUser'], $secrets['mysqlPass']);

if ($development) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $config['URL'] = 'http://localhost:8080';
}

require('./get.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = file_get_contents('php://input');
    // $response = new stdClass;

    if (isset(json_decode($data)->newsletter)) {
        $mailchimpData = [
        'status' => 'subscribed',
        'email_address' => json_decode($data)->email,
        'marketing_permissions' =>[
            [
                'marketing_permission_id' => 'bb56c8bbd1',
                'text' => 'Elfogadom',
                'enabled' => true
                ]
            ]
        ];
        $mailchimpData = json_encode($mailchimpData);
        //$mailchimpData ='email_address=' . json_decode($data)->email . '&status=subscribed';
        $ch = curl_init($secrets['mailchimpUrl']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $mailchimpData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode('user:' . $secrets['mailchimpApiKey']),
            'Content-Length: ' . strlen($mailchimpData)
            ],
        );
        $result = curl_exec($ch);
        // $response->newsletter = $result;
    }

    if (isset(json_decode($data)->email)) {
        $orderId = uniqid();
        $data = json_decode($data);

        $amount = ($data->type == 'tematic') ? $prices['adult'] * $data->adult + $prices['child'] * $data->child : $prices['herbs'] * $data->adult;

        // save order to the database
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stmt = $pdo->prepare("INSERT INTO orders (id, date, adult, child, name, email, phone, newsletter, amount, referrer) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bindValue(1, $orderId);
        $stmt->bindValue(2, $data->date);
        $stmt->bindValue(3, $data->adult);
        $stmt->bindValue(4, $data->child);
        $stmt->bindValue(5, $data->name);
        $stmt->bindValue(6, $data->email);
        $stmt->bindValue(7, $data->phone);
        $stmt->bindValue(8, $data->newsletter);
        $stmt->bindValue(9, $amount);
        $stmt->bindValue(10, $data->referrer);
        $stmt->execute();

        // create simple form
        $simple = new SimplePayStart;
        $simple->addConfig($config);

        $timeoutInSec = 600;
        $simple->addData('timeout ', date("c", time() + $timeoutInSec));

        $simple->addData('currency', 'HUF');
        $simple->addData('total', $amount);
        $simple->addData('orderRef', $orderId);
        $simple->addData('customerEmail', $data->email);
        $simple->addData('language', 'HU');

        $simple->addData('methods', ['CARD']);
        $simple->addData('url', $config['URL']);

        $simple->addGroupData('invoice', 'name', $data->name);
        $simple->addGroupData('invoice', 'company', '');
        $simple->addGroupData('invoice', 'country', 'hu');
        $simple->addGroupData('invoice', 'state', 'Budapest');
        $simple->addGroupData('invoice', 'city', 'Budapest');
        $simple->addGroupData('invoice', 'zip', '1111');
        $simple->addGroupData('invoice', 'address', '-');
        $simple->addGroupData('invoice', 'address2', '');
        $simple->addGroupData('invoice', 'phone', $data->phone);

        $simple->formDetails['elementText'] = 'Bankkártyás fizetés';
        $simple->runStart();
        $simple->getHtmlForm();
        echo $simple->returnData['form'];
        return;
    }

    if (isset($_REQUEST['ipn'])) {
        $json = file_get_contents('php://input');
        $simple = new SimplePayIpn;
        $simple->addConfig($config);
        if ($simple->isIpnSignatureCheck($json)) {
            $simple->runIpnConfirm();
        }
        return;
    }

    if (isset(json_decode($data)->used)) {
        $used = json_decode($data)->used;
        $stmt = $pdo->prepare("UPDATE orders
            SET used = NOT used
            WHERE id = ?");
        echo $stmt->execute([$used]);
        return;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $data = file_get_contents('php://input');
    $stmt = $pdo->prepare("DELETE FROM orders
                WHERE id = ?
                AND payed = 0
                AND used = 0");
    $stmt->execute([json_decode($data)->delete]);
    echo $data;
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
    $data = file_get_contents('php://input');

    if (isset(json_decode($data)->setPayed)) {
        $stmt = $pdo->prepare("UPDATE orders
                    SET payed = 1,
                    transactionId = 'manual'
                    WHERE id = ?");
        $stmt->execute([json_decode($data)->setPayed]);
    }

    if (isset(json_decode($data)->newSpecialDay)) {
        $handle = fopen($fileSpecialDays, "a");
        fwrite($handle, "\n" . json_decode($data)->newSpecialDay);
        fclose($handle);
    }

    if (isset(json_decode($data)->newDate)) {
        $stmt = $pdo->prepare("UPDATE orders
                    SET date = ?
                    WHERE id = ?");
        $stmt->execute([json_decode($data)->newDate ,json_decode($data)->booking]);
        $data = json_decode($data);
        $data->changedRecords = $stmt->rowCount();
        $data = json_encode($data);
    }

    echo $data;
    return;
}