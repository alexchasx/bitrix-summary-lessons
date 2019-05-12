<?php
// примеры запуска:                     http://int.samsonopt.ru/db.php?dev
// по последним цифрам IP и имени БД:   http://int.samsonopt.ru/db.php?30=samson_db

class FileDbConn
{
    const FRAGMENT_IP = '192.168.10.';
    const FRAGMENT_NAME_DB = 'samson_';
    const ERROR_SEARCH_PARAMETERS = 'Ошибка поиска параметров в файле ';
    const ERROR_EMPTY_QUERY = 'Пустой запрос';
    const MESSAGE_SUCCESS = 'Параметры подключения к БД изменены.<br><br>';

    private $arIPDefault = [
        'dev' => '10',
        'test' => '20',
        'etalon' => '30',
    ];
    private $arFiles = [
        'php_interface/dbconn',
        '.settings',
    ];
    private $styleError = '<style type="text/css">
        body {
            font-size: 120%;
            font-family: Verdana, Arial, Helvetica, sans-serif;
            color: red;
        }
        </style>';
    private $styleSuccess = '';
    private $dir = '';
    private $errorMessage = '';
    private $fileCurrent = '';
    private $link = '';

    public function __construct()
    {
        if (!$_GET) {
            exit($this->styleError . self::ERROR_EMPTY_QUERY);
        }
        $this->dir = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/';
        $this->errorMessage .= $this->styleError;
        $this->styleSuccess = str_replace('red', 'green', $this->styleError);
    }

    /**
     * @param string $defaultName
     *
     * @return string
     */
    protected function getContent(string $defaultName): string
    {
        $this->fileCurrent = $this->dir . $defaultName .'_extra.php';
        if (!file_exists($this->fileCurrent)) {
            $this->fileCurrent =  $this->dir . $defaultName . '.php';
        }
        $this->setLink($defaultName, $this->fileCurrent);

        return file_get_contents($this->fileCurrent);
    }

    /**
     * @param        $queryKey
     * @param string $queryValue
     *
     * @return array
     */
    protected function setParamConnect($queryKey, string $queryValue = ''): array
    {
        if (isset($this->arIPDefault[$queryKey])) {
            $dbHost = self::FRAGMENT_IP . $this->arIPDefault[$queryKey];
            $dbName = self::FRAGMENT_NAME_DB . $queryKey;
        } elseif (is_int($queryKey) && preg_match('/^[\w\d]+$/', $queryValue)) {
            $dbHost = self::FRAGMENT_IP . $queryKey;
            $dbName = $queryValue;
        } else {
            $this->errorMessage .= 'Ошибка. Неверно:<br>$DBHost = ' . self::FRAGMENT_IP . $queryKey
                . '<br>$DBName = ' . $queryValue;

            return [];
        }
        return [
            'host' => $dbHost,
            'database' => $dbName,
        ];
    }

    /**
     * @param string $fileDefault
     * @param string $fileCurrent
     */
    protected function setLink(string $fileDefault, string $fileCurrent)
    {
        $this->link .= '<a href="/db.php?watch=' . $fileDefault . '">Посмотреть файл "' . $fileCurrent . '"</a><br>';
    }

    /**
     * @param string $contentFile
     * @param array  $arParams
     *
     * @return bool
     */
    public function writeFile(string $contentFile, array $arParams): bool
    {
        if (strpos($this->fileCurrent, '.settings') !== false) {
            $patternHost = '/[\'\"]host[\'\"]\s*=>\s*[\'\"][\w\.\d]+[\'\"],/';
            $patternDbName = '/[\'\"]database[\'\"]\s=>\s[\'\"][\w\.\d]+[\'\"],/';
            $replaceHost = '\'host\' => \'' . $arParams['host'] . '\',';
            $replaceDatabase = '\'database\' => \'' . $arParams['database'] . '\',';
        } else {
            $patternHost = '/\$DBHost\s=\s[\'\"][\w\.\d]+[\'\"];/';
            $patternDbName = '/\$DBName\s=\s[\'\"][\w\d]+[\'\"];/';
            $replaceHost = '$DBHost = \'' . $arParams['host'] . '\';';
            $replaceDatabase = '$DBName = \'' . $arParams['database'] . '\';';
        }
        if(preg_match($patternHost, $contentFile, $arDbHosts)
            && preg_match($patternDbName, $contentFile, $arDbNames)
        ) {
            $resultContent = preg_replace(
                [
                    $patternHost,
                    $patternDbName,
                ], [
                    $replaceHost,
                    $replaceDatabase,
                ],
                $contentFile,
                1
            );
        } else {
            $this->errorMessage .= self::ERROR_SEARCH_PARAMETERS . $this->fileCurrent;

            return false;
        }

        return (bool)file_put_contents($this->fileCurrent, $resultContent);
    }

    /**
     * @return string
     */
    public function changeFiles(): string
    {
        $queryKey = array_keys($_GET)[0];
        $queryValue = $_GET[$queryKey];

        if ($queryKey == 'watch') {
            return htmlspecialchars($this->getContent($queryValue));
        }
        $arParams = $this->setParamConnect($queryKey, $queryValue);
        if (empty($arParams)) {
            return $this->errorMessage;
        }
        foreach ($this->arFiles as $fileName) {
            if (false == $this->writeFile($this->getContent($fileName), $arParams)) {
                return $this->errorMessage;
            }
        }

        return $this->styleSuccess . self::MESSAGE_SUCCESS . $this->link;
    }
}

echo '<pre>', (new FileDbConn())->changeFiles(), '</pre>';
