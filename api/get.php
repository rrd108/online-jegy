<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  if (isset($_GET['categories'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY position");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
  }

  if (isset($_GET['products'])) {
    $stmt = $pdo->prepare("SELECT * FROM products ORDER BY price");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
  }

  if (isset($_GET['search'])) {
    $stmt = $pdo->prepare("SELECT *
    FROM orders
    WHERE id LIKE ?
    OR name LIKE ?
    OR email LIKE ?");
    $stmt->execute(['%'.$_GET['search'].'%', '%'.$_GET['search'].'%', '%'.$_GET['search'].'%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
  }

  if (isset($_GET['prices'])) {
    echo json_encode($prices);
  }

  if (isset($_GET['maxSlots'])) {
    echo json_encode($maxSlots);
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
    echo $maxSlots[$_GET['type']] - $result['visitors'];
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