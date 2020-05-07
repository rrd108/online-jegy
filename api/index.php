<?php
use PDO;

$development = true;

// TODO do not responde to api calls without authentication

require('./secrets.php');
require('./simplepay/config.php');
require('./simplepay/SimplePayment.class.php');

$adultPrice = 4000;
$childPrice = 3000;

if($development) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $config['BACK_REF'] = 'localhost:8080';
	$config['TIMEOUT_URL'] = 'localhost:8080&timeout=1';
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
        $simple = new SimpleLiveUpdate($config, 'HUF');
        $simple->logFunc(
            'SimpleObjectCreation',
            ['version' => '0.0.1'],
            $orderId
        );
        $simple->addProduct([
            'name' => 'online jegy rendelés',
            'code' => $data->date,
            'info' => 'Felnőtt: ' . $data->adult . ', gyerek: ' . $data->child,
            'price' => $adultPrice * $data->adult + $childPrice * $data->child,
            'qty' => 1,
            'vat' => 0, // no VAT $order_data['prices_include_tax']
        ]);
        $simple->setField('BILL_FNAME', $data->name);
        $simple->setField('BILL_LNAME', $data->name);
        $simple->setField('BILL_EMAIL', $data->email); // must be valid address
        $simple->setField('BILL_PHONE', $data->phone);
        $simple->setField('BILL_ADDRESS', 'Sehol u 0');
        $simple->setField('BILL_CITY', 'Budapest');
        $simple->setField('BILL_STATE', 'Bp');
        $simple->setField('BILL_ZIPCODE', 1234);
        $simple->setField('BILL_COUNTRYCODE', 'HU');

        $simple->setField('DELIVERY_COUNTRYCODE', 'HU');
        $simple->setField('DELIVERY_FNAME', $data->name);
        $simple->setField('DELIVERY_LNAME', $data->name);
        $simple->setField('DELIVERY_ADDRESS', 'Sehol u 0');
        $simple->setField('DELIVERY_PHONE', $data->phone);
        $simple->setField('DELIVERY_ZIPCODE', 1234);
        $simple->setField('DELIVERY_CITY', 'Budapest');
        $simple->setField('DELIVERY_STATE', 'Bp');

        $simple->setField('ORDER_REF', $orderId);
        $simple->setField('ORDER_SHIPPING', 0);

        $simple->setField('DISCOUNT', 0);

        $simple->setField('BACK_REF', $config['BACK_REF']);
        $simple->setField('TIMEOUT_URL', $config['TIMEOUT_URL']);
        $simple->setField('LANGUAGE', 'HU');
        $simple->setField('CURRENCY', 'HUF');

        echo $simple->createHtmlForm('SimplePayForm', 'button', 'Ugrás a fizető oldalra');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_REQUEST['timeout'])) {
        $orderCurrency = $_REQUEST['order_currency'];
        $timeOut = new SimpleLiveUpdate($config, $orderCurrency);

        $timeOut->formData['ORDER_REF'] = $_REQUEST['order_ref'];

        $response = new stdClass;

        if (isset($_REQUEST['redirect'])) {
            $response->error = 'Megszakítottad a tranzakciót, fizetés nem történt';
            $log['TRANSACTION'] = 'ABORT';
        } else {
            $response->error = 'A tranzakcióhoz szükséges idő limit lejárt, fizetés nem történt';
            $log['TRANSACTION'] = 'TIMEOUT';
        }

        $log['ORDER_ID'] = (isset($_REQUEST['order_ref'])) ? $_REQUEST['order_ref'] : 'N/A';
        $log['CURRENCY'] = (isset($_REQUEST['order_currency'])) ? $_REQUEST['order_currency'] : 'N/A';
        $log['REDIRECT'] = (isset($_REQUEST['redirect'])) ? $_REQUEST['redirect'] : '0';
        $timeOut->logFunc("Timeout", $log, $log['ORDER_ID']);
        $timeOut->errorLogger();

        $response->simpleTransactionId = '-';
        $response->orderId = $log['ORDER_ID'];
        $response->date = '-';

        echo json_encode($response);
        return;
    }
    if (isset($_REQUEST['ctrl'])) {
        $orderCurrency = $_REQUEST['order_currency'];
        $backref = new SimpleBackRef($config, $orderCurrency);

        $backref->order_ref = $_REQUEST['order_ref'];

        $response = new stdClass;

        //some error before the user even redirected to SimplePay
        if (!empty($_REQUEST['err'])) {
            $backStatus = $backref->backStatusArray;
            $backref->logFunc("BackRef", $_REQUEST, $backref->order_ref);
            $response->error = $_REQUEST['err'];
        }

        //card authorization failed
        if (empty($_REQUEST['err']) && !$backref->checkResponse()) {
            $backStatus = $backref->backStatusArray;
            $response->error = 'Checksum error';
        }

        //success on card authorization
        if (empty($_REQUEST['err']) && $backref->checkResponse()) {
            $backStatus = $backref->backStatusArray;
            $response->status = $backStatus['ORDER_STATUS'];
        }

        $backref->errorLogger();

        $response->simpleTransactionId = $backStatus['PAYREFNO'];
        $response->orderId = $backStatus['REFNOEXT'];
        $response->date= $backStatus['BACKREF_DATE'];

        echo json_encode($response);
        return;
    }
    // TODO check if ipn called via POST?
    if (isset($_REQUEST['ipn'])) {
        $orderCurrency = $_REQUEST['CURRENCY'];
        $ipn = new SimpleIpn($config, $orderCurrency);
        if ($ipn->validateReceived()) {
            $ipn->confirmReceived();
            // TODO update status of the order
        }
        $ipn->errorLogger();
        return;
    }
}