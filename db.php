<?php
// пример запуска:    http://int.samsonopt.ru/db.php?dev
// по IP и имени БД:  http://int.samsonopt.ru/db.php?30=samson_new

if (!$_GET) {
	exit('Пустой запрос');
}


$styleError = $styleSucces = '';
// $styleError = '<style type="text/css">
//    body { 
//     font-size: 100%; 
//     font-family: Verdana, Arial, Helvetica, sans-serif; 
//     color: red;
//     text-align: center;
//    }
//   </style>';
// $styleSucces = str_replace('red', 'green', $styleError);

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

$fileName = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/dbconn.php';
$arFile = file($fileName);

$resultString = 'Новые параметры подключения: <br>';
array_walk($arFile, function(&$item, $key) use ($dbHost, $dbName, &$resultString) {
    if(preg_match('/^\$DBHost\s=\s[\'|\"][\w\.\d]+[\'|\"];\s*$/', $item)) {
        $item = '$DBHost = \'' . $dbHost . '\';' . PHP_EOL;
        $resultString .= $item . '<br>';
    } elseif (preg_match('/^\$DBName\s=\s[\'|\"][\w\d]+[\'|\"];\s*$/', $item)) {
        $item = '$DBName = \'' . $dbName . '\';' . PHP_EOL;
        $resultString .= $item . '<br>';
    }
});
unset($item);

if (false === file_put_contents($fileName, $arFile)) {
	echo $styleError . error_get_last()['message'];
	exit;
}

echo $styleSucces . $resultString;
exit;
