<?php 
$rest_json = file_get_contents("php://input");
$_POST = simplexml_load_string(json_decode($rest_json, true));


// Подключаемся к базе MySQL и выбираем базу под названием xmldb
$mysqli = new mysqli('127.0.0.1', 'root', '', 'xmldb');
$sql = "CREATE TABLE IF NOT EXISTS `XML` (
  `ID` int(11) NOT NULL,
  `S_NUM_REG` TEXT NOT NULL,
  `DATE_REG` DATE NOT NULL,
  `S_ORG_NAME` TEXT ,
  `S_ORG_COUNTRY` TEXT ,
  `S_TORG_NAME` TEXT,
  `S_MNN` TEXT,
  `S_PROD_PHASE` TEXT,
  `S_DOCS` TEXT ,
  `S_CODES` TEXT ,
  `DATE_END_REG` DATE,
  `DATE_CANCEL_REG` DATE,
  `S_FORMS` TEXT
);";
$mysqli->query($sql); // пробуем создать таблицу если ее нет
if ($mysqli->connect_errno) {
    echo "Извините, возникла проблема на сайте";
    if ($mysqli->connect_errno == 1049) {
      echo "Базы с именем xmldb не существует";
    }
    exit;
  }
  $xml = $_POST->children();
foreach ($xml->children() as $row) { // Выполняем запрос SQL
  $ID = strval($row->ID);                   // Деструктуризация
  $S_NUM_REG = strval($row->S_NUM_REG);
  $DATE_REG = date("Y-m-d", strtotime($row->DATE_REG));  // форматирование даты для таблицы sql
  $S_ORG_NAME = strval($row->S_ORG_NAME);
  $S_ORG_COUNTRY = strval($row->S_ORG_COUNTRY);
  $S_TORG_NAME = strval($row->S_TORG_NAME);
  $S_MNN = strval($row->S_MNN);
  $S_PROD_PHASE = strval($row->S_PROD_PHASE);
  $S_DOCS = strval($row->S_DOCS);
  $S_CODES = strval($row->S_CODES);
  $DATE_END_REG = date("Y-m-d", strtotime($row->DATE_END_REG));
  $DATE_CANCEL_REG = date("Y-m-d", strtotime($row->DATE_CANCEL_REG));
  $S_FORMS = strval($row->S_FORMS);
  
  if($ID != '' && $S_NUM_REG != '' && $DATE_REG != '') {
    $sql = "INSERT INTO XML(ID, S_NUM_REG, DATE_REG, S_ORG_NAME, S_ORG_COUNTRY, S_TORG_NAME, S_MNN, S_PROD_PHASE, S_DOCS, S_CODES, DATE_END_REG, DATE_CANCEL_REG, S_FORMS) 
    VALUES ('" . $ID ."','" . $S_NUM_REG . "','" . $DATE_REG . "','" . $S_ORG_NAME . "','" . $S_ORG_COUNTRY . "','" . $S_TORG_NAME . "','" . $S_MNN . "','" . $S_PROD_PHASE . "','" . $S_DOCS . "','" . $S_CODES . "','" . $DATE_END_REG . "','" . $DATE_CANCEL_REG . "','" . $S_FORMS . "')"; // данные добавляются при каждом импорте как новые.
    $mysqli->query($sql);
    $successRow++;
  } else {
    $affectedRow++;
  }
}
if (!$result = $mysqli->query($sql)) {    // обработка ошибки, (уязвимость) на реальном проекте убрать
    echo "Извините, возникла проблема в работе сайта.";

    echo "Ошибка: Наш запрос не удался и вот почему: \n";
    echo "Запрос: " . $sql . "\n";
    echo "Номер ошибки: " . $mysqli->errno . "\n";
    echo "Ошибка: " . $mysqli->error . "\n";
    echo "COUNTER: " . $counter . "\n";
    exit;
}
echo "Всего строк: ".sizeof($xml->children())."\n"."Успешно записано: " . $successRow . "\n" . "Строки не подходящие под правила: " . $affectedRow;

$mysqli->close(); // отключение соединиения с бд
?>