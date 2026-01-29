<?php
$db = @mysqli_connect(
	getenv('MYSQL_HOST'),
    getenv('MYSQL_USER'),
    getenv('MYSQL_PASSWORD'),
    getenv('MYSQL_DATABASE')
) or die('Error connecting to database');
		//or die('Error connecting to database: ' . mysqli_connect_error()); // for debugging