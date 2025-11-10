<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda</title>
</head>

<body>
    <h1>Búsqueda de libros</h1>

    <form method="GET">
        <label for="busqueda">Introduzca los términos de búsqueda: </label>
        <input type="search" name="busqueda" id="busqueda" required>
        <button type="submit">Buscar</button>
    </form>
</body>

</html>
<?php
if (isset($_GET["busqueda"])) {
    $terminos_busqueda = $_GET["busqueda"];
    if (trim($terminos_busqueda) !== "") {

        require_once "connection.php";

        try {
            // (1) Crear la conexión
            $con = getConnection();

            // (2) Preparar la consulta
            //En la bd bookdb no importan mayúsculas/minúsculas porque está usando collation caseinsensitive, pero no está demás que nuestro código no dependa de la collation de la base de datos
            $stmt = $con->prepare("select title from books where UPPER(title) like ? ");

            // (3) Sustituir de los parámetros
            $stmt->bindValue(1, "%" . strtoupper($terminos_busqueda) . "%");

            //Antes de ejecutar: 
            // echo "<p style='color:blue;'> Información de <code>debugDumpParams</code> <span style='color:red'> antes </span> de llamar a  <code>execute()</code>:</p>";
            // echo "<pre>";
            // $stmt->debugDumpParams();
            // echo "</pre>";

            // (4) Ejecutar la consulta
            $stmt->execute();

            //Después de ejecutar
            // echo "<p style='color:blue;'> Información de <code>debugDumpParams</code> <span style='color:red'> después </span> de llamar a  <code>execute()</code>:</p>";
            // echo "<pre>";
            // $stmt->debugDumpParams();
            // echo "</pre>";

            // (5) Recuperar los resultados
            $array = $stmt->fetchAll(PDO::FETCH_NUM);
            // if (($array !== false)) {
            if (!empty($array)) {

                echo "<ol>";
                foreach ($array as $fila_array) {
                    // un único valor: el title
                    echo "<li> $fila_array[0] </li>";
                }
                echo "</ol>";
            } else {
                echo "<p>No se han encontrado resultados</p>";
            }
            //}

            //(6) Capturar excepciones
        } catch (Exception $e) {
            echo "<p>Ha ocurrido una excepción: " . $e->getMessage() . "</p>";
        } finally {
            //(7) 
            // Liberar los recursos
            $con = null;
            $stmt = null;
        }
    } else {
        echo "<p> Introduzca una cadena no vacía </p>";
    }
}

?>