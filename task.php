<?php 
$menu=array("contacts","meetings","calls");
function databaseConnect(){
    $conn = mysqli_connect('localhost','root','root','php') or die('database connection failed');
    return $conn;
}
mainmenu($menu);
function mainmenu($menu){
	echo "Please select the module\n";
	$length=count($menu);
	$count=0;
	foreach ($menu as $value) {
		$count=$count+1;
		echo "choose $count to select $value\n";
	}
	$choice=readline();
	$selected=intval($choice);
	$status=validateChoice($selected, $length);
	if ($status==true) {
		$modulename=$menu[$selected-1];
		submenu($modulename);
	}
	else
		return(mainmenu($menu));
}
function validateChoice($choice, $length){
	if ($choice>0 && $choice<=$length) {
		return true;
	}
	else
		echo "please enter vlaid choice\n";
		return false;
}
function submenu($modulename){
	$actions=array("create","view","edit","importing","delete");
	$length=count($actions);
	echo "Enter your choice to select one of the submenu\n";
	$count=0;
	foreach ($actions as $value) {
		$count=$count+1;
		echo "select $count to $value $modulename\n";
	}
	$choice=readline();
	$modulechoice=intval($choice);
	$status=validateChoice($modulechoice, $length);
	if ($status==true) {
		$action=$actions[$modulechoice-1];
		submenudetails($modulename, $action);
	}
	else
		return(submenu($modulename));
}
function submenudetails($modulename, $action){
	echo "$action $modulename \n";
	$fields=array();
	$connection=databaseConnect();
	$sql="select * from $modulename";
	$result=mysqli_query($connection, $sql) or die("error in retrieving data".mysqli_error($connection));
	while ($fieldinfo=mysqli_fetch_field($result)) {
		array_push($fields, $fieldinfo->name);
	}
	foreach ($fields as $value) {
		echo "$value\n";
	}
	switch ($modulename) {
		case 'contacts':
		if ($action=="create") {
			createcontact($fields, $modulename);
		}
		elseif ($action=="view") {
			viewcontact();
		}
		elseif ($action=="edit") {
			editcontact();
		}
		elseif ($action=="importing") {
			importcontact();
		}
		else{
			deletecontact();
		}
		break;
		case 'meetings':
			if ($action=="create") {
			schedulemeeting();
		}
		elseif ($action=="view") {
			viewmeeting();
		}
		elseif ($action=="edit") {
			editmeeting();
		}
		elseif ($action=="importing") {
			importmeeting();
		}
		else{
			deletemeeting();
		}
			break;
		case 'calls':
		if ($action=="create") {
			logcall();
		}
		elseif ($action=="view") {
			viewcalls();
		}
		elseif ($action=="edit") {
			editcalls();
		}
		elseif ($action=="importing") {
			importcalls();
		}
		else{
			deletecalls();
		}
			
			break;
		}
}
function createcontact($fields, $modulename){
	$fieldarray=$fields;
	$fieldvalues=array();
	$fieldscount=count($fieldarray);
	foreach($fieldarray as $value) {
		echo "please enter $value\n";
		$fieldvalue=readline();
		array_push($fieldvalues, "$fieldvalue");
	}
	$valuestring=implode("','", $fieldvalues);
	echo "$valuestring\n";
	$connection=databaseConnect();
	$query="INSERT INTO contacts values('$valuestring')";
	$result=mysqli_query($connection, $query) or die("error in inserting values\n". mysqli_error($connection));
	mysqli_close($connection);
	echo "Please enter 1 if you want to continue with creating records\n ";
	echo "enter 2 to exit";
	$case=readline();
	switch ($case) {
		case '1':
			return(createcontact($fields,$modulename));
			break;
		case '2':
			exit();
			break;
		default:
			break;
	}
}
function viewcontact(){
	$conn = new mysqli('localhost','root','root','php');
	if ($conn->connect_error) {
		die("connection failed:" . $conn->connect_error);
	}
	$sql = "SELECT firstname, lastname, mobilenumber, email FROM contacts";
	$result = $conn->query($sql);
			if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				echo "\n". $row["firstname"]." " . $row["lastname"] ." \n ". $row["mobilenumber"] . " \n ". $row["email"] . "";
			}
		}
}
function editcontact(){
	$connection=databaseConnect();
	echo "Enter the field name which you want to edit\n";
	$edit=readline();
	switch ($edit) {
		case 'firstname':
			echo "Enter your mobilenumber\n";
			$editname=readline();
			echo "Enter your firstname\n";
			$efirstname=readline();
			echo "$efirstname";
			$query="update contacts set firstname='$efirstname' where mobile_number='$editname'";
			$result=mysqli_query($connection,$query) or die('error in updating data');
			mysqli_close($connection);
			echo "Edited values are saved in database\n";
			break;
		case 'lastname':
			echo "Enter your mobilenumber\n";
			$editname=readline();
			echo "Enter lastname\n";
			$elastname=readline();
			$query="update contacts set lastname='$elastname' where mobile_number='$editname'";
			$result=mysqli_query($connection,$query) or die('error in updating data');
			mysqli_close($connection);
			echo "Edited values are saved in database\n";
			break;
		case 'mobilenumber':
			echo "Enter your firstname\n";
			$editname=readline();
			echo "Enter Mobile Number\n";
			$emobilenumber=readline();
			$query="update contacts set mobile_number='$emobilenumber' where firstname='$editname'";
			$result=mysqli_query($connection,$query) or die('error in updating data');
			echo "Edited values are saved in database\n";
			break;
		case 'email':
			echo "Enter your firstname\n";
			$editname=readline();
			echo "Enter email\n";
			$eemail=readline();
			$query="update contacts set email='$eemail' where firstname='$editname'";
			$result=mysqli_query($connection,$query) or die('error in updating data');
			echo "Edited values are saved in database\n";
			break;
		
		default:
			
			break;
	echo "Edited values are saved in database\n";
	$query="update contacts set firstname='$efirstname', lastname='$elastname', mobilemumber='$emobilenumber',email='$eemail' where [$edit]";
	$result=mysqli_query($connection,$query);
	}	
}
function importcontact(){	
 $conn = mysqli_connect('localhost','root','root','php');
 if($file = fopen('/var/www/html/webpage/php/contacts.csv', "r")){
        while (($importdata = fgetcsv($file, 10000, ",")) !== FALSE)
        {
           $sql = "INSERT into contacts(firstname, lastname, mobilenumber, email) values('$importdata[0]','$importdata[1]','$importdata[2]','$importdata[3]')";
           mysqli_query($conn,$sql);
        }
        fclose($file);
        echo "CSV File has been successfully Imported.\n";
		}
		else { 
			echo "could not open";
		}   
} 
function deletecontact(){
	$conn=databaseConnect();
	if ($conn->connect_error) {
		die("connection failed: ". $conn->connect_error);
	}
	echo "enter your firstname\n";
	$delete=readline();
	$sql = "DELETE from contacts WHERE firstname='$delete'";
	if ($conn->query($sql) === TRUE) {
		echo "Record deleted successfully";
	}
	else {
		echo "Error in deleting Record:" .$conn->error;
	}
	$conn->close();
}
function schedulemeeting(){
	echo "Enter the id\n";
	$id=readline();
	echo "Enter the subject\n";
	$subject=readline();
	echo "Related To\n";
	$relatedto=readline();
	echo "Enter Start Date\n";
	$startdate=readline();
	echo "Enter End Date\n";
	$enddate=readline();
	$connection=databaseConnect();
	echo "Thanks for giving details\n";
	echo "Your id is '$id'";
	$query="insert into meetings values('$id','$subject','$relatedto','$startdate','$enddate')";
	$result=mysqli_query($connection, $query) or die('error in inserting data');
	mysqli_close($connection);
}
function viewmeeting(){
	$conn = new mysqli('localhost','root','root','php');
	if ($conn->connect_error) {
		die("connection failed:" . $conn->connect_error);
	}
	$sql = "SELECT id, subject, relatedto, startdate, enddate FROM meetings";
	$result = $conn->query($sql);
			if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				echo "\n ".$row["id"]."\n". $row["subject"]."\n ". $row["relatedto"]."\n" . $row["startdate"] ." \n ". $row["enddate"] . " \n ";
			}
		}
}
function importmeeting(){
	$conn=mysqli_connect('localhost', 'root', 'root') or die(mysqli_error($sql));
	$connection=databaseConnect();
	mysqli_select_db($connection,'php') or die(mysqli_error());
	$filename = "/var/www/html/webpage/php/Test.csv";
	$ext=substr($filename,strrpos($filename,"."),(strlen($filename)-strrpos($filename,".")));
	if ($ext==".csv") {
		$file = fopen($filename, "r");
		while (($emapDate = fgetcsv($file, 10000, ",")) !== FALSE) {
			$sql = "INSERT into meetings(subject,startdate,enddate) values('$emapDate[0]','$emapDate[1]','$emapDate[2]')";
			mysqli_query($conn,$sql);
		}
		fclose($file);
         echo "CSV File has been successfully Imported.";
	}
		else {
    	echo "Error: Please Upload only CSV File";
    }
}
function deletemeeting(){
	$conn=databaseConnect();
	if ($conn->connect_error) {
		die("connection failed: ". $conn->connect_error);
	}
	echo "please enter your id\n";
	$delete=readline();
	$sql = "DELETE from meetings WHERE subject='$delete'";
	if ($conn->query($sql) === TRUE) {
		echo "Record deleted successfully";
	}
	else {
		echo "Error in deleting Record:" .$conn->error;
	}
	$conn->close();
}
function logcall(){
	echo "Enter the subject\n";
	$subject=readline();
	echo "Enter Start Date\n";
	$startdate=readline();
	echo "Enter End Date\n";
	$enddate=readline();
	$connection=databaseConnect();
	echo "Thanks for giving details\n";
	$query="insert into calls values('$subject','$startdate','$enddate')";
	$result=mysqli_query($connection, $query) or die('error in inserting data');
	mysqli_close($connection);
}
function viewcalls(){
	$conn = new mysqli('localhost','root','root','php');
	if ($conn->connect_error) {
		die("connection failed:" . $conn->connect_error);
	}
	$sql = "SELECT subject, startdate, enddate FROM calls";
	$result = $conn->query($sql);
			if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				echo "\n". $row["subject"]."\n " . $row["startdate"] ." \n ". $row["enddate"] . " \n ";
			}
		}
}
?>
