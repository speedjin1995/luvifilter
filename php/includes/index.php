<?php
session_start();

if(!isset($_SESSION['userDetail'])){
	echo '<script type="text/javascript">location.href = "../../login.html";</script>'; 
} 

?>