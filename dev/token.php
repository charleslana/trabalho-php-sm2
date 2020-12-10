<?php
session_start();
if(isset($_SESSION['tokenEntidade'])) {
	if(isset($_GET['del'])) {
		unset($_SESSION['tokenEntidade']);
		echo 'Token deleted';
		header("location: ../");
	}
	else {
		echo $_SESSION['tokenEntidade'];
	}
}