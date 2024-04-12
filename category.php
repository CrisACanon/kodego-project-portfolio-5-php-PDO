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
        $sql = "SELECT * FROM category";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE cat_Id = :cat_Id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cat_Id', $path[3]);
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
        $sql = "INSERT INTO category (cat_Id, category) VALUES(null, :category)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':category', $user->category);
  
        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record created successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to create record.'];
        }
        echo json_encode($message);
        break;

    case "PUT":
        $user = json_decode( file_get_contents('php://input') );
        $sql = "UPDATE category SET category= :category  WHERE cat_Id = :cat_Id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cat_Id', $user->cat_Id);
        $stmt->bindParam(':category', $user->category);

        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record updated successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to update record.'];
        }
        echo json_encode($message);
        break;

    case "DELETE":
        $sql = "DELETE FROM category WHERE cat_Id = :cat_Id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cat_Id', $path[3]);

        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        echo json_encode($message);
        break;
}