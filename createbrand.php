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
       $destination= $_SERVER['DOCUMENT_ROOT']."/pdo-php-api/brands"."/";
       $allbrand= mysqli_query($db_conn, "SELECT * FROM brands");
       if(mysqli_num_rows($allbrand) > 0)
       {
          while($row= mysqli_fetch_array($allbrand))
          {
           $json_array["branddata"][]= array("id"=>$row['brand_id'], 
           "brand"=>$row["brand"],
           "brand_image"=>$row["brand_image"],

          );
          }
          echo json_encode($json_array["branddata"]);
          return;
       } else {
        echo json_encode(["result"=>"please check the Data"]);
        return;
       }

      }
    
    break;

    case "POST":
      if(isset($_FILES['brand_image']))
      {      
        $brand= $_POST['brand'];
        $bfile= time().$_FILES['brand_image']['name'];
        $bfile_temp= $_FILES['brand_image']['tmp_name'];
        $destination= $_SERVER['DOCUMENT_ROOT'].'/pdo-php-api/brands'."/".$bfile;

        $result= mysqli_query($db_conn,"INSERT INTO brands (brand, brand_image)
        VALUES('$brand','$bfile')");

        if($result)
        { 
          move_uploaded_file($bfile_temp, $destination);
          echo json_encode(["success"=>"Brand Inserted Successfully"]);
           return;
        } else{
          echo json_encode(["success"=>"Brand Not Inserted!"]);
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