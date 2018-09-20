<?php
#mac
if($_SERVER['HTTP_HOST'] == MAC_URL) {
    $dsn = array(
        'phptype'  => 'mysql',
        'username' => 'root',
        'password' => '979899',
        'hostspec' => 'localhost',
        'database' => 'saoroque_com_br',
        'charset'  => 'utf8mb4'
    );
}

// #tests
if($_SERVER['HTTP_HOST'] == TEST_URL) {
   $dsn = array(
        'phptype'  => 'mysql',
        'username' => 'root',
        'password' => '',
        'hostspec' => '127.0.0.1',
        'database' => 'saoroque_com_br',
        'charset'  => 'utf8mb4',
    );
}

#production
if(in_array($_SERVER['HTTP_HOST'], $PRODUCTION_URLS)) {
    $dsn = array(
        'phptype'  => 'mysql',
        'username' => 'root',
        'password' => '',
        'hostspec' => '127.0.0.1',
        'database' => 'saoroque_com_br',
        'charset'  => 'utf8mb4',
    );
}

$options = array(
    'debug'       => 2,
    'portability' => MDB2_PORTABILITY_ALL
);

/* SE NAO CONECTAR
TENTA CONECTAR LOCALMENTE COM SENHAS COMUNS */
if($_SERVER['HTTP_HOST'] == DEVEVOPMENT_URL) {
    if (MDB2::connect($dsn, $options)->message == 'MDB2 Error: connect failed'){
        $dsn_pass[0] = 'root';
        $dsn_pass[1] = '';
        $dsn_pass[2] = '979899';

        foreach ($dsn_pass as $key => $dsn['password']) {
            if (MDB2::connect($dsn, $options)->message != 'MDB2 Error: connect failed'){
                $mdb2 = MDB2::connect($dsn, $options);
                break;
            }
        }
    } else {
        $mdb2 = MDB2::connect($dsn, $options);
    }
} else {
    $mdb2 = MDB2::connect($dsn, $options);
}

if (PEAR::isError($mdb2)) {
    die('error:'.$mdb2->getMessage());
}
?>
