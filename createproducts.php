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
       $destination= $_SERVER['DOCUMENT_ROOT']."/pdo-php-api/products"."/";
       $allproduct= mysqli_query($db_conn, "SELECT * FROM products");
       if(mysqli_num_rows($allproduct) > 0)
       {
          while($row= mysqli_fetch_array($allproduct))
          {
           $json_array["productdata"][]= array("id"=>$row['prod_id'], 
           "ptitle"=>$row["prod_title"],
           "pprice"=>$row["prod_price"],
           "pimage"=>$row["image"],


          );
          }
          echo json_encode($json_array["productdata"]);
          return;
       } else {
        echo json_encode(["result"=>"please check the Data"]);
        return;
       }
      }
     
    break;

    case "POST":
      if(isset($_FILES['image']))
      {      
        $ptitle= $_POST['prod_title'];
        $pprice= $_POST['prod_price'];
        $pfile= time().$_FILES['image']['name'];
        $pfile_temp= $_FILES['image']['tmp_name'];
        $prod_desc= $_POST['prod_desc'];
        $prod_specs= $_POST['prod_specs'];
        $brandId= $_POST['brand_id'];

        $destination= $_SERVER['DOCUMENT_ROOT'].'/pdo-php-api/products'."/".$pfile;

        $result= mysqli_query($db_conn,"INSERT INTO products (prod_title, prod_price, image, prod_desc, prod_specs, brand_id)
        VALUES('$ptitle', '$pprice', '$pfile', '$prod_desc', '$prod_specs', '$brandId')");

        if($result)
        { 
          move_uploaded_file($pfile_temp, $destination);
          echo json_encode(["success"=>"Product Inserted Successfully"]);
           return;
        } else{
          echo json_encode(["success"=>"Product Not Inserted!"]);
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