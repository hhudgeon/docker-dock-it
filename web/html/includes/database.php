<?php
$db = mysqli_connect(
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASSWORD'),
    getenv('DB_DATABASE')
) or die('Error connecting to database');

