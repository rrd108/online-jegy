<?php

//use PDO;

// TODO do not respond to api calls without authentication check if we have a valid token

require('./headers.php');
require('./secrets.php');
require('./simplepay/config.php');
require('./simplepay/SimplePayV21.php');

// no programs on these days
$fileSpecialDays = './specialDays';
$handle = fopen($fileSpecialDays, 'r');
$specialDays = explode("\n", fread($handle, filesize($fileSpecialDays)));
fclose($handle);

$pdo = new PDO('mysql:host=' . $secrets['mysql']['host'] . ';dbname=' . $secrets['mysql']['table'], $secrets['mysql']['user'], $secrets['mysql']['pass']);

if ($development) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $config['URL'] = 'http://localhost:8080';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = file_get_contents('php://input');
    // $response = new stdClass;

    if (isset(json_decode($data)->product) && !isset(json_decode($data)->date)) {
        // add / edit product
        $products = json_decode(file_get_contents('products.json'));
        $product = json_decode($data)->product;
        $i = json_decode($data)->i;

        if ($i == -1) {
            array_push($products, $product);
        } else {
            $products[$i] = $product;
        }
        $handle = fopen('products.json', 'w');
        fwrite($handle, json_encode($products));
        fclose($handle);

        echo $data;
    }

    if (isset(json_decode($data)->product) && isset(json_decode($data)->date)) {
        // add edit day
        $days = json_decode(file_get_contents('days.json'));
        $day = json_decode($data);
        array_push($days, ['date' => $day->date, 'product' => $day->product]);

        $handle = fopen('days.json', 'w');
        fwrite($handle, json_encode($days));
        fclose($handle);

        echo $data;
    }

    if (isset(json_decode($data)->newsletter)) {
        $mailchimpData = [
            'status' => 'subscribed',
            'email_address' => json_decode($data)->email,
            'marketing_permissions' => [
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
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode('user:' . $secrets['mailchimpApiKey']),
                'Content-Length: ' . strlen($mailchimpData)
            ],
        );
        $result = curl_exec($ch);
        // $response->newsletter = $result;
    }

    if (isset(json_decode($data)->email)) {
        $orderId = uniqid();
        $data = json_decode($data);

        // $data->type
        $products = json_decode(file_get_contents('products.json'));
        $product = array_values(array_filter($products, function ($product) use ($data) {
            return $product->product == $data->type;
        }))[0];
        $amount = $product->adult * $data->adult + $product->child * $data->child;

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

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['search'])) {
        $stmt = $pdo->prepare("SELECT *
            FROM orders
            WHERE id LIKE ?
            OR name LIKE ?
            OR email LIKE ?");
        $stmt->execute(['%' . $_GET['search'] . '%', '%' . $_GET['search'] . '%', '%' . $_GET['search'] . '%']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }

    if (isset($_GET['products'])) {
        echo file_get_contents('./products.json');
    }

    if (isset($_GET['days'])) {
        $_days = json_decode(file_get_contents('./days.json'));
        // we only need days from today 
        // TODO delete former days
        $days = [];
        foreach ($_days as $day) {
            if (strtotime($day->date) > strtotime('today')) {
                $days[$day->date] = $day->product;
            }
        }
        krsort($days);
        echo json_encode($days);
    }

    if (isset($_GET['specialDays'])) {
        echo json_encode($specialDays);
    }

    if (isset($_GET['checkins'])) {
        // TODO payed and not payed should be different
        $stmt = $pdo->prepare("SELECT date AS id, date AS startDate, CONCAT(DATE_FORMAT(date, '%H'), '-', (IFNULL(SUM(adult),0) + IFNULL(SUM(child),0)), ' fő') AS title
            FROM orders
            WHERE date >= ?
            GROUP BY date
            ORDER BY date");
        $stmt->execute([date('Y-m-d', strtotime('first day of last month'))]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }

    if (isset($_GET['visitors'])) {
        // TODO payed and not payed should be different
        $stmt = $pdo->prepare("SELECT *
            FROM orders
            WHERE date = ?");
        $stmt->execute([$_GET['visitors']]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }

    if (isset($_GET['slots'])) {
        $stmt = $pdo->prepare("SELECT IFNULL(SUM(adult),0) + IFNULL(SUM(child),0) AS visitors
            FROM orders
            WHERE date = ?
            AND payed = 1");
        $stmt->execute([$_GET['slots']]);
        $result = $stmt->fetch();
        echo $result['visitors'];
    }

    // simplepay back
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
        if ($result['e'] == 'CANCEL') {
            $response->error = true;
        }

        if ($result['e'] == 'SUCCESS') {
            $stmt = $pdo->prepare("UPDATE orders
                SET payed = 1
                WHERE id = ?");
            $stmt->execute([$result['o']]);

            $stmt = $pdo->prepare("SELECT *
                FROM orders
                WHERE id = ?");
            $stmt->execute([$result['o']]);
            $order = $stmt->fetch();

            // from the order date we can get the product info
            $days = json_decode(file_get_contents('days.json'));
            foreach ($days as $day) {
                if ($day->date == substr($order['date'], 0, 10)) {
                    $product = $day->product;
                    break;
                }
            }

            $products = json_decode(file_get_contents('products.json'));
            foreach ($products as $p) {
                if ($p->product == $product) {
                    $product = $p;
                    break;
                }
            }

            require('./payment-success.php');

            $message = str_replace('{{name}}', $order['name'], $message);
            $message = str_replace('{{orderId}}', $result['o'], $message);
            $message = str_replace('{{tourTime}}', $order['date'], $message);
            $message = str_replace('{{adult}}', $order['adult'] ?: 0, $message);
            $message = str_replace('{{child}}', $order['child'] ?: 0, $message);
            $message = wordwrap($message, 70, "\r\n");
            $headers[] = 'From: jegy@krisnavolgy.hu';
            $headers[] = 'Bcc: jegy@krisnavolgy.hu';
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            mail($order['email'], 'Krisna-völgy túra jegy rendelés', $message, implode("\r\n", $headers));
        }

        $response->status = $events[$result['e']];
        $response->orderId = $result['o'];
        $response->simpleTransactionId = $result['t'];
        $response->amount = isset($order['amount']) ? $order['amount'] : 0;
        echo json_encode($response);
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
        $stmt->execute([json_decode($data)->newDate, json_decode($data)->booking]);
        $data = json_decode($data);
        $data->changedRecords = $stmt->rowCount();
        $data = json_encode($data);
    }

    echo $data;
    return;
}
