<?php

error_reporting( E_ALL & ~E_NOTICE );

$data = mysqli_connect("localhost", "root", "", "school");
if( mysqli_connect_error( ) ){
	echo mysqli_connect_error( );exit;
}
if($_POST['action'] == "load_states"){

	$city = [];
	$query = "select * from city order by state";
	$res = mysqli_query($data, $query);
	while( $row = mysqli_fetch_assoc( $res) ){
		$city[] = $row;
	}
	echo json_encode($city);
	exit;
}
if($_POST['action'] == "add_state"){
	$query = "insert into city set
	state = '" .mysqli_escape_string($data,$_POST['state'] ) . "' ";
	mysqli_query($data,$query);
	if(mysqli_error($data) ){
		echo "fail";
		exit;
	}
	echo "success";
	exit;
}

if( $_POST['action'] == "delete_state"){
	$query = "delete from city where id =" . $_POST['state_id'];
	$res = mysqli_query($data, $query);
	if( mysqli_error($data) ){
		echo "fail";
		exit;
	}
	echo "success";
	exit;
}
if( $_POST['action'] == "edit_state" ){
	$query = "update city set 
	state = '" . mysqli_escape_string($data, $_POST['state'] ) . "'
	where id = " . $_POST['state_id'];
	mysqli_query($data, $query);
	if( mysqli_error($data) ){
		echo "fail";
		exit;
	}
	echo "success";
	exit;
}

?>

<html>
<head>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" >
</head>
<body>

<div id="state_add_form_div" style="position: absolute; display: none; top: 100px; left: 100px;" >
<div class="card" >
	<div class="card-title" >Add State</div>
	    <button type="button" onclick="hide_add_state_form()"></button>
	<table width="100%">
		<tbody>
			<tr>
				<td>States</td>
				<td><input type="text" id="state"></td>
			</tr>
		</tbody>
	</table>
	        <button class="btn btn-secondary btn-sm" onclick="hide_add_state_form()">Close</button>
	        <input type="button" class="btn btn-primary btn-sm" value="ADD" onclick="add_state()">
	      </div>
</div>
</div>

<div id="state_edit_form_div" style="position: absolute; display: none; top: 100px; left: 100px;" >
<div class="card" >
	<div class="card-title" >Edit State</div>
	<div class="card-body" >
        <button type="button" onclick="hide_edit_state_form()"></button>
	</div>
		<table width="100%">
			<tbody>
			<tr>
				<td>State</td>
				<td><input type="text" id="edit_state"></td>
			</tr>
		   </tbody>
	    </table>
	      </div>
	      <div class="modal-footer">
	        <button class="btn btn-secondary btn-sm" onclick="hide_edit_state_form()">Close</button>
	        <input type="button" class="btn btn-primary btn-sm" value="EDIT" onclick="save_state()">
	      </div>
	    </div>


<table class="table table-bordered table-sm w-auto">
	<tr>
		<td>States</td>
		<td>Cities</td>
		<td>Areas</td>
	</tr>
	<tr>
		<td>
			<div><input type="button" class="btn btn-info btn-sm" value="+" onclick="show_add_state_form()" ></div>
			<table class="table table-bordered table-striped table-sm" >
				<tr>
					<td>State</td>
					<td>Edit/Delete</td>
				</tr>	
				 <tbody id="states_list_data" >
				</tbody>
			</table>
		</td>
	</tr>	
</table>

<script>
	function show_add_state_form(){
		document.getElementById("state_add_form_div").style.display = 'block';
	}
	function hide_add_state_form(){
		document.getElementById("state_add_form_div").style.display = 'none';
	}
function add_state(){
	state = document.getElementById("state").value;
	vpostdata = "action=add_state&state=" + encodeURIComponent(state); 

	data = new XMLHttpRequest();
	data.open( "POST", "ajax2.php", true );
	data.onload = function(){
		if( this.responseText == "success" ){
			load_states();
		}else{
			alert("There was an error at server");
		}
	}
	data.setRequestHeader("content-type", "application/x-www-form-urlencoded");
	data.send( vpostdata );

}
var states_list = [];
function load_states(){
	data = new XMLHttpRequest();
	data.open( "POST", "ajax2.php", true );
	data.onload = function(){
		states_list = JSON.parse( this.responseText );
		console.log( states_list );
		generate_states();
	}
	data.setRequestHeader("content-type", "application/x-www-form-urlencoded");
	data.send( "action=load_states" );
}
function generate_states(){
	var vstr = "";
	for(i=0;i<states_list.length;i++){
		vstr += `<tr>
		<td>`+states_list[i]['state']+`</td>
		<td>
			<input type="button" value="E" onclick="show_edit_state_form(`+i+`)" >
			<input type="button" value="X" onclick="delete_state(`+i+`)" >
		</td>
		</tr>`;
	}
	console.log( vstr );
	document.getElementById("states_list_data").innerHTML = vstr;
}
var editing_state_id = 0;
function hide_edit_state_form(){
	document.getElementById("state_edit_form_div").style.display = 'none';
}
function show_edit_state_form(vi){
	editing_state_id = vi;
	document.getElementById("state_edit_form_div").style.display = 'block';
	document.getElementById("edit_state").value = states_list[vi]['state'];
}
function save_state(){
	states_list[editing_state_id]['state'] = document.getElementById("edit_state").value;

	vpostdata = "action=edit_state";
	vpostdata += "&state_id=" +states_list[editing_state_id]['id'];
	vpostdata += "&state=" +states_list[editing_state_id]['state'];

	data = new XMLHttpRequest();
	data.open( "POST", "test_ajax_4.php", true );
	data.onload = function(){
		if( this.responseText == "success" ){
			hide_edit_state_form();
			generate_states();
		}else{
			alert("There was an error");		
		}
	}
	data.setRequestHeader("content-type", "application/x-www-form-urlencoded");
	data.send( vpostdata );
}
deleting_state_id = 0;
function delete_state(vi){
	deleting_state_id = vi;
	data = new XMLHttpRequest();
	data.open( "POST", "ajax2.php", true );
	data.onload = function(){
		if( this.responseText == "success" ){
			states_list.splice( deleting_state_id,1 );
			generate_states();
		}else{
			alert("There was an error");		
		}
	}
	data.setRequestHeader("content-type", "application/x-www-form-urlencoded");
	data.send( "action=delete_state&state_id=" + states_list[vi]['id'] );	
}
load_states();
</script>
</body>
</html>