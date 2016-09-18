<?php
    include_once('common.php');

    $postedData = json_decode(file_get_contents("php://input"), true);
    $data = json_decode($postedData['data']);
    $pronostiqueur = $data->Pronostiqueur;

    $sql =      '   CALL            sp_effacementpronostiqueur(:pronostiqueur, 0)';

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':pronostiqueur', $pronostiqueur);

    $errorMessage = '';
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        $errorMessage = 'Error during execution of move operation: ' . $e->getMessage();
    }
    finally {
        //$stmt->close();
    }

    echo json_encode($errorMessage);
?>