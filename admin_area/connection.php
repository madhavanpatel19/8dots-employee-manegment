<?php  
$host="localhost";
$user="root";
$pass="";
$db="8dots";

$con=mysqli_connect($host,$user,$pass,$db);

if ($con)
{
 echo "";  
}
else
{
 echo"not record found";    
}
?>