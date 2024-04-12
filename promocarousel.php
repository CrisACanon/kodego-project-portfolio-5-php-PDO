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
        $sql = "SELECT * FROM promocarousel";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE promo_Id = :prodtype_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':promo_Id', $path[3]);
            $stmt->execute();
            $users = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($users);
        break;
    case "POST":
        $user = json_decode( file_get_contents('php://input') );
        $sql = "INSERT INTO promocarousel (promo_Id, promo_des) VALUES(null, :prodtype)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':promo_des', $user->prodtype);

  
        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record created successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to create record.'];
        }
        echo json_encode($message);
        break;

    case "PUT":
        $user = json_decode( file_get_contents('php://input') );
        $sql = "UPDATE promocarousel SET promo_des= :promo_des WHERE promo_Id = :promo_Id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':prodtype_id', $user->prodtype_id);
        $stmt->bindParam(':promo_des', $user->promo_des);
     

        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record updated successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to update record.'];
        }
        echo json_encode($message);
        break;

    case "DELETE":
        $sql = "DELETE FROM promocarousel WHERE promo_Id = :promo_Id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':promo_Id', $path[3]);

        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        echo json_encode($message);
        break;
}