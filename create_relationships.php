<?php

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;

require __DIR__.'/vendor/autoload.php';

// Conectar a Neo4j
$url = 'bolt://localhost:7687?database=neo4j';
$auth = Authenticate::basic('neo4j', 'password');
$client = ClientBuilder::create()->withDriver('neo4j', $url, $auth)->build();

try {
    // Consulta para encontrar nodos con la misma edad y crear relaciones entre ellos
    $client->run('MATCH (p1:Person), (p2:Person) WHERE p1.age = p2.age AND id(p1) <> id(p2) CREATE (p1)-[:EDAD_IGUAL]->(p2)');

    $message = 'Relaciones creadas con éxito';
} catch (\Exception $e) {
    $message = 'Error: ' . $e->getMessage();
    error_log($message);
}

// Devolver una respuesta al cliente
header('Content-Type: application/json');
echo json_encode(['message' => $message]);
?>
