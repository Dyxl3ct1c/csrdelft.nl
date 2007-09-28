<?php
# Mootlijsten maken

$jaar = '07';

require_once('include.config.php');

if(!$lid->hasPermission('P_ADMIN')){
	header('location: '.CSR_ROOT);
	exit;
}

# databaseconnectie openen
$db=MySql::get_MySql();
$lid=Lid::get_lid();

echo <<<EOD
<table cellpadding="15">
	<tr valign="top">
EOD;

for($i=1; $i<=4; $i++){
	$result = $db->select("SELECT uid FROM `lid` WHERE moot=".$i." AND (status='S_LID' OR status='S_GASTLID' OR status='S_NOVIET' OR status='S_KRINGEL')");
	if ($result !== false and $db->numRows($result) > 0) {
		echo '<td><h2>Moot '.$i.'</h2><pre>';
		
		while($row = $db->next($result)){
			echo $row['uid']."@csrdelft.nl\n";
		}
		
		echo '</pre></td>';
	}
}

echo <<<EOD
	</tr>
</table>
EOD;

?>
