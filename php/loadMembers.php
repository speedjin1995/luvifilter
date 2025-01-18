<?php
## Database configuration
require_once 'includes/db_connect.php';

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";
if($searchValue != ''){
   $searchQuery = " and (users.name like '%".$searchValue."%' or 
        users.email like '%".$searchValue."%' or 
        users.email like '%".$searchValue."%' or 
        roles.role_name like'%".$searchValue."%' ) ";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from users");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];



## Total number of record with filtering
//echo "select count(*) as allcount from users, roles WHERE".$searchQuery;
$sel = mysqli_query($db,"select count(*) as allcount from users, roles WHERE users.role_code = roles.role_code".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select users.id, users.name, users.email, users.joined_date, users.expired_date, users.status, roles.role_name from users, roles WHERE users.role_code = roles.role_code".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
    $joined_date = '-';
    $expired_date = '-';
    
    if($row['joined_date'] != null && $row['joined_date'] != ""){
		$joined_date = date("d-m-Y", strtotime($row['joined_date']));
	}
    
    if($row['expired_date'] != null && $row['expired_date'] != ""){
		$expired_date = date("d-m-Y", strtotime($row['expired_date']));
	}
    
    $data[] = array( 
        "id"=>$row['id'],
        "name"=>$row['name'],
        "email"=>$row['email'],
        "role_name"=>$row['role_name'],
        "joined_date"=>$joined_date,
        "expired_date"=>$expired_date,
        "status"=>$row['status']
    );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);

?>