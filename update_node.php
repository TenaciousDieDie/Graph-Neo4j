<?php

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;

require __DIR__.'/vendor/autoload.php';

// Recibir datos del formulario
$data = json_decode(file_get_contents('php://input'), true);
$currentName = $data['current_name']; // Nombre del nodo que deseas actualizar
$newName = $data['new_name']; // Nuevo nombre
$newAge = $data['new_age']; // Nueva edad

// Conectar a Neo4j
$url = 'bolt://localhost:7687?database=neo4j';
$auth = Authenticate::basic('neo4j', 'password');
$client = ClientBuilder::create()->withDriver('neo4j', $url, $auth)->build();

try {
    // Buscar el nodo que deseas actualizar
    $result = $client->run('MATCH (p:Person {name: $currentName})-[r:SHARES_AGE_WITH]->() DELETE r', compact('currentName'));

    // Actualizar las propiedades del nodo
    $result = $client->run('MATCH (p:Person {name: $currentName}) SET p.name = $newName, p.age = $newAge RETURN p', compact('currentName', 'newName', 'newAge'));

    if ($result->count() > 0) {
        // Establecer nuevas relaciones con nodos que tengan la misma edad
        $matchingNodesResult = $client->run('MATCH (p:Person {age: $newAge}) WHERE p.name <> $newName RETURN p', compact('newAge', 'newName'));

        foreach ($matchingNodesResult as $matchingNodeRecord) {
            $matchingNode = $matchingNodeRecord->get('p');
            $client->run('MATCH (p1:Person {name: $newName}), (p2:Person {name: $matchingNode.name}) CREATE (p1)-[:SHARES_AGE_WITH]->(p2)', compact('newName', 'matchingNode'));
        }

        $message = 'Nodo actualizado con éxito y relaciones establecidas.';
    } else {
        // El nodo no existe
        $message = 'No se encontró un nodo con el nombre proporcionado.';
    }
} catch (\Exception $e) {
    $message = 'Error: ' . $e->getMessage();
}

// Devolver una respuesta al cliente
header('Content-Type: application/json');
echo json_encode(['message' => $message]);

/* 
use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;

require __DIR__.'/vendor/autoload.php';

// Recibir datos del formulario
$data = json_decode(file_get_contents('php://input'), true);
$currentName = $data['current_name']; // Nombre del nodo que deseas actualizar
$newName = $data['new_name']; // Nuevo nombre
$newAge = $data['new_age']; // Nueva edad

// Conectar a Neo4j
$url = 'bolt://localhost:7687?database=neo4j';
$auth = Authenticate::basic('neo4j', 'password');
$client = ClientBuilder::create()->withDriver('neo4j', $url, $auth)->build();

try {
    // Buscar el nodo que deseas actualizar
    $result = $client->run('MATCH (p:Person {name: $currentName}) RETURN p', compact('currentName'));

    if ($result->count() > 0) {
        // El nodo existe, actualizar sus propiedades
        $client->run('MATCH (p:Person {name: $currentName}) SET p.name = $newName, p.age = $newAge', compact('currentName', 'newName', 'newAge'));
        $message = 'Nodo actualizado con éxito.';
    } else {
        // El nodo no existe
        $message = 'No se encontró un nodo con el nombre proporcionado.';
    }
} catch (\Exception $e) {
    $message = 'Error: ' . $e->getMessage();
}

// Devolver una respuesta al cliente
header('Content-Type: application/json');
echo json_encode(['message' => $message]);
*/
?>
