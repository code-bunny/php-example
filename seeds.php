<?php

$pdo = new PDO('mysql:host=127.0.0.1;dbname=mydb', 'user', 'pass', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$posts = [
    ['Getting Started with PHP', 'PHP is a widely-used open source scripting language suited for web development. It is fast to learn and runs on the server side.'],
    ['Understanding Variables', 'Variables in PHP start with a dollar sign. They can hold strings, integers, floats, booleans, arrays, and more.'],
    ['Working with Arrays', 'Arrays are one of the most useful data types in PHP. You can create indexed arrays, associative arrays, and even multidimensional arrays.'],
    ['Control Flow in PHP', 'PHP supports if/else, switch, while, for, and foreach statements. These let you control the flow of your program based on conditions.'],
    ['Functions and Scope', 'Functions let you reuse blocks of code. Variables inside a function are scoped locally unless declared global.'],
    ['Connecting to a Database', 'PHP uses PDO (PHP Data Objects) to connect to databases like MySQL. It provides a consistent interface and supports prepared statements.'],
    ['What Are Prepared Statements?', 'Prepared statements protect your app from SQL injection by separating SQL code from user data. Always use them when handling user input.'],
    ['Building HTML Forms', 'HTML forms send data to your server via GET or POST. In PHP you can read that data from the $_GET and $_POST superglobals.'],
    ['Sessions and Cookies', 'Sessions let you store user data across requests on the server. Cookies store small pieces of data in the browser.'],
    ['Introduction to MVC', 'MVC stands for Model-View-Controller. It is a pattern for organising code that separates data logic, display, and user input handling.'],
];

$stmt = $pdo->prepare("INSERT INTO posts (title, body) VALUES (?, ?)");

foreach ($posts as [$title, $body]) {
    $stmt->execute([$title, $body]);
}

echo "Seeded " . count($posts) . " posts.\n";
