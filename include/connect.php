<?php

$con=mysqli_connect("localhost","root","","kevinscollection1");
if(!$con){
//     echo"connection success";
// }else{
    die(mysqli_error($con));
}
