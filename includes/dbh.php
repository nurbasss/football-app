<?php
//connect to oracle db

$conn = oci_connect('fbproject', '123', 'localhost/XE')
	or die(oci_error());
if(!$conn){
	echo "Sorry problems";
}
