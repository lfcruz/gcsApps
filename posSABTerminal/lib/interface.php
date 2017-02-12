<?php
error_reporting(-1);
session_start();

include 'ldapClass.php';
include 'refreshdata.php';

if(!isset($_POST['name'])) {
    echo "Can not access this page without login in."; 
    exit(1); 
}
if(isset($_POST['submit'])) {
    if(($_POST['name'])=="" || ($_POST['password'])=="") {
        echo "Username and Password must be provided.";
    }
 }

$user = $_POST['name'] ;
$password = $_POST['password'];
$activeD = new ldapComm();


if (!$activeD->authUser($user, $password) and !array_key_exists('pstrGroup', $activeD->getGroups($user))) {
    echo "<script type='text/javascript'>alert('Invalid Username or Password, or your credentials has no access.');window.location.href='../index.php'</script>";
    exit(1);
}
$_SESSION['username'] = $user;
$_SESSION['expiretime'] = date("H:i", strtotime('+1 minutes'));
refreshdata();
?>
