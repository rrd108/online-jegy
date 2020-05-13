<?php
use PDO;

$development = true;

// TODO do not responde to api calls without authentication

require('./secrets.php');
require('./simplepay/config.php');
require('./simplepay/SimplePayV21.php');

$pdo = new PDO('mysql:host=localhost;dbname=' . $secrets['mysqlTable'], $secrets['mysqlUser'], $secrets['mysqlPass']);
$adultPrice = 4000;
$childPrice = 3000;
$maxSlots = 50;

if ($development) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $config['URL'] = 'http://localhost:8080';
}

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = file_get_contents('php://input');
    // $response = new stdClass;

    if (isset(json_decode($data)->newsletter)) {
        $ch = curl_init($secrets['mailchimpUrl']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode('user:' . $secrets['mailchimpApiKey']),
            'Content-Length: ' . strlen($data)
            ],
        );
        $result = curl_exec($ch);
        // $response->newsletter = $result;
    }

    if (isset(json_decode($data)->email)) {
        $orderId = uniqid();
        $data = json_decode($data);

        $amount = $adultPrice * $data->adult + $childPrice * $data->child;

        // save order to the database
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stmt = $pdo->prepare("INSERT INTO orders (id, date, adult, child, name, email, phone, newsletter, amount) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bindValue(1, $orderId);
        $stmt->bindValue(2, $data->date);
        $stmt->bindValue(3, $data->adult);
        $stmt->bindValue(4, $data->child);
        $stmt->bindValue(5, $data->name);
        $stmt->bindValue(6, $data->email);
        $stmt->bindValue(7, $data->phone);
        $stmt->bindValue(8, $data->newsletter);
        $stmt->bindValue(9, $amount);
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
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['checkins'])) {
        $stmt = $pdo->prepare("SELECT date, (SUM(adult) + SUM(child)) AS visitors
            FROM orders
            WHERE date >= ?
            GROUP BY date
            ORDER BY date");
        $stmt->execute([date('Y-m-01')]);
        $result = $stmt->fetchAll();
        echo json_encode($result);
    }

    if (isset($_GET['slots'])) {
        $stmt = $pdo->prepare("SELECT (SUM(adult) + SUM(child)) AS visitors
            FROM orders
            WHERE date = ?");
        $stmt->execute([$_GET['slots']]);
        $result = $stmt->fetch();
        echo $maxSlots - $result['visitors'];
    }

    if (isset($_REQUEST['r']) && isset($_REQUEST['s'])) {
        $simple = new SimplePayBack;
        $simple->addConfig($config);

        $result = [];
        if ($simple->isBackSignatureCheck($_REQUEST['r'], $_REQUEST['s'])) {
            $result = $simple->getRawNotification();
        }

        $stmt = $pdo->prepare("UPDATE orders
            SET transactionId = ?
            WHERE id = ?");
        $stmt->execute([$result['t'], $result['o']]);

        $events = [
            'SUCCESS' => 'Sikeres tranzakció',
            'FAIL' => 'Sikertelen tranzakció',
            'TIMEOUT' => 'Időtúllépés, nem lett a megadott ideig elindítva a fizetés',
            'CANCEL' => 'Megszakított fizetés'
        ];

        $response = new stdClass;

        if ($result['e'] != 'SUCCESS') {
            $response->error = 'Kérjük, ellenőrizze a tranzakció során megadott adatok helyességét! Amennyiben minden adatot helyesen adott meg, a visszautasítás okának kivizsgálása érdekében kérjük, szíveskedjen kapcsolatba lépni kártyakibocsátó bankjával.';
        }

        if ($result['e'] == 'SUCCESS') {
            $stmt = $pdo->prepare("UPDATE orders
                SET payed = 1
                WHERE id = ?");
            $stmt->execute([$result['o']]);
        }

        $response->status = $events[$result['e']];
        $response->orderId = $result['o'];
        $response->simpleTransactionId = $result['t'];
        echo json_encode($response);
        return;
    }

    // TODO check if ipn called via POST?
    if (isset($_REQUEST['ipn'])) {
        $json = file_get_contents('php://input');
        $simple = new SimplePayIpn;
        $simple->addConfig($config);
        if ($simple->isIpnSignatureCheck($json)) {
            $simple->runIpnConfirm();
        }
        return;
    }
}