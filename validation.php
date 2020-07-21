<?php include "module.php";
session_start();

$connection = connect_to_db("test");
date_default_timezone_set('Asia/Kolkata');

    
extract($_POST);


$error = "";
echo $error;


// echo $email= $_POST["email"];
// echo $user = $_POST["username"];
// echo $pass = $_POST["pass"];
// echo $repass = $_POST["repass"];
// echo $dob = $_POST["dob"];
// echo $city = $_POST["city"];
// echo $state = $_POST["state"];
// echo $mobile = $_POST["mobile"];
// echo $firstName = $_POST["first"];
// echo $lastName = $_POST["last"];
// echo $title = $_POST["title"];

if(isset($_POST["signup"])){
    
    $email = mysqli_real_escape_string($connection, $email);
$pass = mysqli_real_escape_string($connection, $pass);
$user = mysqli_real_escape_string($connection, $username);
$dob = mysqli_real_escape_string($connection, $dob);
$city = mysqli_real_escape_string($connection, $city);
$state = mysqli_real_escape_string($connection, $state);
$firstName = mysqli_real_escape_string($connection, $first);
$lastName = mysqli_real_escape_string($connection, $last);
$title = mysqli_real_escape_string($connection, $title);
$mobile = mysqli_real_escape_string($connection, $mobile);
    
   $query = "SELECT `id` FROM login WHERE `email`='$email'";
    $result = "";
    if(!($result = mysqli_query($connection, $query))){
        printf("Error: %s\n", mysqli_error($connection));
        exit();   
    }
    if(mysqli_num_rows($result)>0){
        $error = "This E-mail id already is already taken.";
        header("location: index.html");
    
    
    }else{
        
                $dp = $_FILES["dp"];
        $dp_name = $dp["name"];
        
        $dp_tmp = $dp["tmp_name"];
        $dp_error = $dp["error"];
        $dp_ext = explode('.',$dp_name);
        $dp_check = strtolower(end($dp_ext));

        $extension = array("jpg", "png", "jpeg"); 
            
        $destination = "";
        if(in_array($dp_check, $extension)){
            $destination = "profilepic/".$dp_name;
            move_uploaded_file($dp_tmp, $destination);
                    }
        
        $createdOn = date('d-m-y h:i:s');
        $query = "INSERT INTO login (user, pass, email, dob, designation, firstname, lastname, mobile, city, state, createdOn, dp)";
        $query .= "VALUES ('$user', '$pass', '$email', '$dob', '$title', '$firstName', '$lastName', $mobile, '$city', '$state', '$createdOn', '$destination')";
        
        if(!($result=mysqli_query($connection, $query))){
            printf("Error: %s\n", mysqli_error($connection));
            exit();  
        }else{
            $query = "SELECT `id` FROM login WHERE `email`='$email'";
            $result = mysqli_query($connection, $query);
            $row = mysqli_fetch_array($result);
            $id = $row[0];
            $hashed = md5(md5($id).$pass);
        
            $query = "UPDATE login SET `pass`='$hashed' WHERE `email`='$email'";
            $final_result="" ;
            
            if($final_result = mysqli_query($connection, $query)){
                
                setcookie('id', $id, time()+ 60*60);
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $user;
                header("Location: redirect.php");
            }
        }
    }
    
}else if(isset($_POST["login"])){
    
  
    if($email!="" and $pass!=""){
        
        $email = mysqli_real_escape_string($connection, $email);
        $pass = mysqli_real_escape_string($connection, $pass);
        $query = "SELECT * FROM login WHERE email='$email'";
        $result = "";
        if(!($result= mysqli_query($connection, $query))){
                echo "First Query failed";
        }
        
        if(mysqli_num_rows($result)>0){
            
            $query = "SELECT * FROM login WHERE email='$email'";
            
            $result="";
            if(!($result= mysqli_query($connection, $query))){
                echo "Second Query failed";
                
                
        }
            
            $password = mysqli_fetch_array($result);
            $md5 = md5(md5($password['id']).$pass);
            
            if($md5 == $password['pass']){
                
                $_SESSION['id'] = $password['id'];
                $_SESSION['username'] = $password['user'];
                
                  
                setcookie('id', $id, time()+ 60*60*24);
                
                header("location: redirect.php");
            }else{
                $error .= "Please Enter Valid Password";
                header("location: index.html");
            }
            
        }else{
              $error.="This E-Mail id is not registered with us";
            header("location: index.html");
        }
        
        
    }
    
}else{
    
    echo "<script>alert('phir se lafda ho gya')</script>";
}

?>