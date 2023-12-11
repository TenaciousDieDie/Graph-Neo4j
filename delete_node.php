<?php

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;

require __DIR__.'/vendor/autoload.php';

// Recibir datos del formulario o de alguna otra fuente
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['name'])) {
    $nameToDelete = $data['name']; // o cualquier otro criterio para identificar el nodo que quieres eliminar

    // Conectar a Neo4j
    $url = 'bolt://localhost:7687?database=neo4j';
    $auth = Authenticate::basic('neo4j', 'password');
    $client = ClientBuilder::create()->withDriver('neo4j', $url, $auth)->build();

    // Consulta para eliminar el nodo y sus relaciones
    $query = 'MATCH (p:Person {name: $nameToDelete}) DETACH DELETE p';

    try {
        $result = $client->run($query, compact('nameToDelete'));

        // Verificar si se eliminó algún nodo
        if ($result->count() > 0) {
            $message = 'Nodo eliminado con éxito';
        } else {
            $message = 'Nodo eliminado con éxitoooo Xd';
        }
    } catch (\Exception $e) {
        $message = 'Error al eliminar nodo: ' . $e->getMessage();
        error_log($message);  // Agrega esta línea para imprimir el mensaje de error en el log de errores de PHP.
    }
} else {
    $message = 'Error: El nombre del nodo no se proporcionó correctamente.';
}

// Devolver una respuesta al cliente
header('Content-Type: application/json');
echo json_encode(['message' => $message]);
//FUNCIONA MASOMENOS
?>
