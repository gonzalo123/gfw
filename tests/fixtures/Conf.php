<?php
return array(
    'DB' => array(
        'MAIN' => array(
            'dsn'      => 'sqlite::memory:',
            'username' => 'username',
            'password' => 'password',
            'options'  => array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        ),
        'PG' => array(
            'dsn'      => 'pgsql:host=127.0.0.1;port=5432;dbname=testdb',
            'username' => 'postgres',
            'password' => 'password',
            'options'  => array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        )
    )
);