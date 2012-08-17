<?php
return array(
    'DB' => array(
        'MAIN' => array(
            'dsn'      => 'sqlite::memory:',
            'username' => 'username',
            'password' => 'password',
            'options'  => array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        )
    )
);