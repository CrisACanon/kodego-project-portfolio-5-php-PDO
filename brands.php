<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

require_once 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch($method) {
    case "GET":
        $sql = "SELECT * FROM brands";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE brand_id = :brand_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':brand_id', $path[3]);
            $stmt->execute();
            $users = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($users);
        break;

    case "PUT":
        $user = json_decode( file_get_contents('php://input') );
        $sql = "UPDATE brands SET brand= :brand, brand_image= :brand_image  WHERE brand_id = :brand_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':brand_id', $user->brand_id);
        $stmt->bindParam(':brand', $user->brand);
        $stmt->bindParam(':brand_image', $user->brand_image);

        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record updated successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to update record.'];
        }
        echo json_encode($message);
        break;

    case "DELETE":
        $sql = "DELETE FROM brands WHERE brand_id = :brand_id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':brand_id', $path[3]);

        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        echo json_encode($message);
        break;
}