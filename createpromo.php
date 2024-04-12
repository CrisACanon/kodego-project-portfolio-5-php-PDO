<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
header("Access-Control-Allow-Origin:* ");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

$db_conn= mysqli_connect("localhost","root", "", "ecomm-db");
if($db_conn===false)
{
  die("ERROR: Could Not Connect".mysqli_connect_error());
}

$method = $_SERVER['REQUEST_METHOD'];
//echo "test----".$method; die;
switch($method)
{
    case "GET": 
      $path= explode('/', $_SERVER['REQUEST_URI']);

      if(isset($path[4]) && is_numeric($path[4]))
      {
        echo "Get Api Single Row"; die;
      } else {
       //echo "return all Data"; die;
       $destination= $_SERVER['DOCUMENT_ROOT']."/pdo-php-api/promo"."/";
       $allbrand= mysqli_query($db_conn, "SELECT * FROM promocarousel");
       if(mysqli_num_rows($allbrand) > 0)
       {
          while($row= mysqli_fetch_array($allbrand))
          {
           $json_array["promodata"][]= array("id"=>$row['promo_Id'], 
           "promo_des"=>$row["promo_des"],
           "promo_image"=>$row["promo_image"],

          );
          }
          echo json_encode($json_array["promodata"]);
          return;
       } else {
        echo json_encode(["result"=>"please check the Data"]);
        return;
       }

      }
    
    break;

    case "POST":
      if(isset($_FILES['promo_image']))
      {      
        $promo_des= $_POST['promo_des'];
        $bfile= time().$_FILES['promo_image']['name'];
        $bfile_temp= $_FILES['promo_image']['tmp_name'];
        $destination= $_SERVER['DOCUMENT_ROOT'].'/pdo-php-api/promo'."/".$bfile;

        $result= mysqli_query($db_conn,"INSERT INTO promocarousel (promo_des, promo_image)
        VALUES('$promo_des','$bfile')");

        if($result)
        { 
          move_uploaded_file($bfile_temp, $destination);
          echo json_encode(["success"=>"Promo Inserted Successfully"]);
           return;
        } else{
          echo json_encode(["success"=>"Promo Type Not Inserted!"]);
           return;
        }

      } else{
        echo json_encode(["success"=>"Data not in Correct Format"]);
        return;
      }
        
    break;

    case "DELETE":
           
    break;

}