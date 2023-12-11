<?php

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
require __DIR__.'/vendor/autoload.php';

$url = 'bolt://localhost:7687?database=neo4j';
$auth = Authenticate::basic('neo4j', 'password');

$client = ClientBuilder::create()->withDriver('neo4j', $url, $auth)->build();

// Crear un nuevo nodo de tipo 'Person'
//$client->run(
//    'CREATE (p:Person {name: $name, age: $age})',
//    ['name' => 'John Doe', 'age' => 30]
//);

//echo 'Nodo creado con éxito.' . PHP_EOL;
?>
