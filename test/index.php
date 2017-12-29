<?php
require_once 'vendor/autoload.php';

$a = new akintunde102\chocolatephp\chocolate;

$string = 'All versions of PHP that support namespaces support three kinds of aliasing or importing: aliasing a class name, aliasing an interface name, and aliasing a namespace name. PHP 5.6+ also allows aliasing or importing function and constant names.';
$wordsreturned = 20;

$sh = $a->shorten_words($string, $wordsreturned);

echo $sh;
?>