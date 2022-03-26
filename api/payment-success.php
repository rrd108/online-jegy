<?php

$message = '<html>
<head>
  <title>Sikeres jegyvásárlás</title>
</head>
<body>
  <h1>Kedves {{name}}!</h1>
  <h2>Köszönjük, hogy jegyet vásároltál a Tematikus Túrára!</h2>
  <p>A rendelési azonosítód: <strong>{{orderId}}</strong></p>
  <p>A túra ideje: <strong>{{tourTime}}</strong></p>
  <p>Létszám: <strong>{{adult}} felnőtt</strong> és <strong>{{child}} gyerek / nyugdíjas</strong></p>
  <p><strong>Belépés a rendelési számod azonosításával történik, kérlek nyomtasd ezt ki, vagy mutasd fel telefonodon.</strong></p>

  <h1>Útvonal</h1>
  <h2>' . $product->product . '</h2>
  <p>' . $product->info . '</p>

  <h1>Fontos infók</h1>
  <p>A kiválasztott jegyek nem válthatóak vissza és csak a kiválasztott időpontban használhatóak fel. A Krisna-völgyi kedvezménykártya ehhez a kiemelt programunkhoz nem biztosít kedvezményt.
 </p>

  <p>Nemsokára találkozunk!</p>
  <p>Minden jót és Gauranga!</p>
  <p><strong>Kundalata Devi Dasi</strong></p>
</body>
</html>';
