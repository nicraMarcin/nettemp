<?php 
$gpioad = isset($_POST['gpioad']) ? $_POST['gpioad'] : '';
$add = isset($_POST['add']) ? $_POST['add'] : '';
$gpio = isset($_POST['gpio']) ? $_POST['gpio'] : '';
$check = '';

if ( $add == "ADD") {
	$db = new PDO('sqlite:dbf/nettemp.db');
	if (!empty($gpioad)) { 
	    $db->exec("INSERT INTO gpio (gpio, name, status) VALUES ('$gpio','new_$gpio','OFF')") or exit(header("Location: html/errors/db_error.php"));
	}
	else {
	    $db->exec("DELETE FROM gpio WHERE gpio='$gpio'") or exit(header("Location: html/errors/db_error.php"));
	}
	$db = NULL;
	header("location: " . $_SERVER['REQUEST_URI']);
	exit();
}
?>
<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title">Free GPIO</h3>
</div>
<div class="panel-body">
<?php
    exec("/usr/local/bin/gpio -v |grep B+", $bplus );
    exec("/usr/local/bin/gpio -v |grep 'Model B, Revision: 2'", $btwo );
    exec("/usr/local/bin/gpio -v |grep 'Model B, Revision: 1'", $bone );
    exec("/usr/local/bin/gpio -v |grep 'Model 2, Revision: 1.1'", $two );
    if ((!empty($bplus[0])) || (!empty($two[0])))
    {
        $gpiolist = array(4,17,27,22,5,6,13,19,26,18,23,24,25,12,16,20,21);
    }
    elseif (!empty($btwo[0]))
    {
        $gpiolist = array(4,17,27,22,18,23,24,25,28,29,30,31);
    }
    elseif (!empty($bone[0]))
    {
        $gpiolist = array(4,17,27,22,18,23,24,25);
    }
    else
    {
	$gpiolist = array(4,17,21,22,18,23,24,25);
    } ?>

<?php
foreach ($gpiolist as $value1) {
	$db = new PDO('sqlite:dbf/nettemp.db');
	$rows = $db->query("SELECT * FROM gpio WHERE gpio='$value1'") or exit(header("Location: html/errors/db_error.php"));
	$row = $rows->fetchAll();
	foreach ($row as $result) { 
   	    $added[] = $result['gpio'];
	}
       if (!in_array($value1, $added)){ ?>
    	 
<form action="" method="post" style=" display:inline!important;">
    <button type="submit" name="gpioad"  value="on" class="btn btn-xs btn-primary" onchange="this.form.submit()" >GPIO <?php echo $value1; ?></button>
    <input type="hidden" name="gpio" value="<?php echo $value1 ?>" />
    <input type="hidden" name="add" value="ADD" />
</form>
<?php  } }?>
    
<span id="helpBlock" class="help-block">Note: Do not use GPIO4 when use 1wire sensors connected to GPIO4 </span>
</div></div>
