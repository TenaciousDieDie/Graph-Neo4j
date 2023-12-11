<?php

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;

require __DIR__.'/vendor/autoload.php';

// Recibir datos del formulario
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'];
$age = $data['age'];

// Conectar a Neo4j
$url = 'bolt://localhost:7687?database=neo4j';
$auth = Authenticate::basic('neo4j', 'password');
$client = ClientBuilder::create()->withDriver('neo4j', $url, $auth)->build();

// Crear nodo en Neo4j
$client->run('CREATE (p:Person {name: $name, age: $age})', compact('name', 'age'));

// Devolver una respuesta al cliente
header('Content-Type: application/json');
echo json_encode(['message' => 'Nodo creado con éxito']);

?>

