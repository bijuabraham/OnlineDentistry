<html>       
<head>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="test.js"></script>
<script>

</script>
<style>
#myProgress {
  width: 80%;
  background-color: #ddd;
}

#myBar {
  width: 1%;
  height: 30px;
  background-color: #4CAF50;
}
</style>
</head>
<body>
<?php
require_once 'od_utils.php';
session_start();
od_authenticate();
od_header();
od_top_menu();
db_connect("CCDB");
$query = "select count(*) as counter_id from mailinglist";
//print $sqlquery2;
$result = db_fetch_all("CCDB", $query);
$counter_id=db_result($result,0,"counter_id");
?>
<span class="span_edit_right">
    <i class="fa fa-trash-o" aria-hidden="false">
        Enter a counter value
    </i>
    <form>
        <input id="counter_id" type = "text" value="<?php echo $counter_id; ?>"/>
        <input type="submit" id="submit" name="Send" value="Send" onclick="move()">
    </form>
</span>
<p>Results:</p>
<div id="myProgress">
  <div id="myBar"></div>
</div>
<p id = "successcount"></p>
<p id = "successmessage"></p>
<p id = "errormessage"></p>
</body>
</html>
