<?php
// пример запуска:                     http://int.samsonopt.ru/db.php?dev
// по последним цифрам IP и имени БД:  http://int.samsonopt.ru/db.php?30=samson_db

if (!$_GET) {
	exit('Пустой запрос');
}

// Стилизация вывода
$styleError = $styleSuccess = '';
// $styleError = '<style type="text/css">
//    body {
//     font-size: 110%;
//     font-family: Verdana, Arial, Helvetica, sans-serif;
//     color: red;
//     text-align: center;
//    }
//   </style>';
// $styleSuccess = str_replace('red', 'green', $styleError);

$queryKey = array_keys($_GET)[0];
$queryValue = $_GET[$queryKey];
$fragmentHost = '192.168.10.';

if (is_string($queryKey)) {
	switch ($queryKey) {
		case 'dev':
			$dbHost = 'localhost_dev';
			$dbName = 'samson_dev';
			break;
		case 'test':
			$dbHost = 'localhost_test';
			$dbName = 'samson_test';
			break;
		case 'etalon':
			$dbHost = 'localhost_etalon';
			$dbName = 'samson_test';
			break;
		case 'localhost':
			$dbHost = 'localhost';
			$dbName = $queryValue;
			break;
		default:
			exit($styleError . 'Неизвестна БД для ' . $queryKey);
			break;
	}	
} elseif (is_int($queryKey) && preg_match('/^[\w\d]+$/', $queryValue)) {
	$dbHost = $fragmentHost . $queryKey;
	$dbName = $queryValue;
} else {
	exit($styleError . 'Ошибка. Неверно: <br>$DBHost = ' . $fragmentHost . $queryKey 
		. '<br>$DBName = ' . $queryValue);
}

$fileDbConn = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/dbconn_extra.php';
if (!file_exists($fileDbConn)) {
    $fileDbConn = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/dbconn.php';
}
$arDbConn = file($fileDbConn);
$resultString = 'Новые параметры подключения: <br>';
$searchSuccess = 0;
array_walk($arDbConn, function(&$item) use ($dbHost, $dbName, &$resultString, &$searchSuccess) {
    if(preg_match('/^\$DBHost\s=\s[\'\"][\w\.\d]+[\'\"];\s*$/', $item)) {
        $item = '$DBHost = \'' . $dbHost . '\';' . PHP_EOL;
        $resultString .= $item . '<br>';
        $searchSuccess++;
    } elseif (preg_match('/^\$DBName\s=\s[\'\"][\w\d]+[\'\"];\s*$/', $item)) {
        $item = '$DBName = \'' . $dbName . '\';' . PHP_EOL;
        $resultString .= $item . '<br>';
        $searchSuccess++;
    }
});
unset($item);
if ($searchSuccess < 2) {
    echo $styleError . 'Ошибка поиска подключения к БД в файле ' . $fileDbConn;
    exit;
}
if (false === file_put_contents($fileDbConn, $arDbConn)) {
	echo $styleError . error_get_last()['message'];
	exit;
}

$fileSettings = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/.settings_extra.php';
if (!file_exists($fileSettings)) {
    $fileSettings = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/.settings.php';
}
$arSettings = file($fileSettings);
$searchSuccess = 0;
array_walk($arSettings, function(&$item) use ($dbHost, $dbName, &$searchSuccess) {
    if(preg_match('/^\s*[\'\"]host[\'\"]\s=>\s[\'\"][\w\.\d]+[\'\"],\s*$/', $item)) {
        $item = '           \'host\' => \'' . $dbHost . '\',' . PHP_EOL;
        $searchSuccess++;
    } elseif (preg_match('/^\s*[\'\"]database[\'\"]\s=>\s[\'\"][\w\.\d]+[\'\"],\s*$/', $item)) {
        $item = '           \'database\' => \'' . $dbName . '\',' . PHP_EOL;
        $searchSuccess++;
    }
});
unset($item);
if ($searchSuccess < 2) {
    echo $styleError . 'Ошибка поиска подключения к БД в файле ' . $fileSettings;
    exit;
}
if (false === file_put_contents($fileSettings, $arSettings)) {
	echo $styleError . error_get_last()['message'];
	exit;
}

echo $styleSuccess . $resultString;
exit;
