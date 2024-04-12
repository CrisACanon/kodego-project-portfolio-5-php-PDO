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
        $sql = "SELECT * FROM contact";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if(isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE contact_id = :contact_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':contact_id', $path[3]);
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
        $sql = "INSERT INTO contact(contact_id, customer_name, email_address, contact_number, message) VALUES(null, :customer_name, :email_address, :contact_number, :message)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':customer_name', $user->customer_name);
        $stmt->bindParam(':email_address', $user->email_address);
        $stmt->bindParam(':contact_number', $user->contact_number);
        $stmt->bindParam(':message', $user->message);
  
        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record created successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to create record.'];
        }
        echo json_encode($message);
        break;

    case "PUT":
        $user = json_decode( file_get_contents('php://input') );
        $sql = "UPDATE contact SET customer_name= :customer_name, email_address =:email_address, contact_number =:contact_number, message =:message  WHERE contact_id = :contact_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':contact_id', $user->contact_id);
        $stmt->bindParam(':customer_name', $user->customer_name);
        $stmt->bindParam(':email_address', $user->email_address);
        $stmt->bindParam(':contact_number', $user->contact_number);
        $stmt->bindParam(':message', $user->message);

        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record updated successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to update record.'];
        }
        echo json_encode($message);
        break;

    case "DELETE":
        $sql = "DELETE FROM contact WHERE contact_id = :contact_id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':contact_id', $path[3]);

        if($stmt->execute()) {
            $message = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $message = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        echo json_encode($message);
        break;
}