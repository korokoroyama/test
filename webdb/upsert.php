<?php
//★★引数継承
if(empty($_POST["table"])){
}else{
	$table=$_POST["table"];
}

if(empty($_POST["id"])){
}else{
	$id=$_POST["id"];
}

if(empty($_POST["flag"])){
}else{
	$flag=$_POST["flag"];
}

//★★htmlヘッダ作成
echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<title>'.$table."確認ページ:".date("y/m/d")."-".date("H:i").'</title></head>';
?>

<style type="text/css">
	body{ font-size: 0.9em; font-family: Arial, Verdana, sans-serif; color:#555; }
	h1{ margin-top: 0; float: left; }
	#controls{ float: left; padding: 0.3em 1em; }
	table.scrollTable{
		width:100%;
		border:1px solid #ddd000;
	}
	thead{
		background-color: #EEE000;
	}
	thead th{
		border-top:1px solid #FFFF00;
		border-right:1px solid #FFFF00;
		text-align: center;
		padding:0.1em 0.3em;
	}
	tbody td{
		border-top:1px solid #eee;
		border-right:1px solid #eee;
		padding:0.1em 0.3em;
                            }
                            tbody tr.odd td{
                                          background-color: #f9f9f9;
                            }
              </style>
<?php
//★★画面内ヘッダ作成
echo "ページ作成日時：".date("Y年m月d日")."-".date("H:i:s")."<br>";
//★★継承
//require 'php/class.php';

//★★DB接続
$dbname="postgres";
$usern="postgres";//接続ユーザ名
$passw="korokoroyama";//ユーザパスワード
$conn = "host=localhost dbname=".$dbname." user=".$usern." password=".$passw;
$link = pg_connect($conn);
if (!$link) {
              die('接続失敗です。'.pg_last_error());
}

//★★SQL作成
$sqlact="select * ";
$sqlact=$sqlact."FROM ".$table;
$sqlact=$sqlact." WHERE ".$table."_id = ".$id;

//★★SQLリスト
echo $sqlact;
echo "<hr>";
//★★SQL実行
$result = pg_query($sqlact);

$updatesql="UPDATE ".$table." SET ";
$insertsql="INSERT INTO ".$table." values (";

echo '<table rules="all">';
echo '<tr>';
echo '<th>カラム</th>';
echo '<th>値</th>';
echo '<th>型</th>';
echo '<th>変更後</th>';
echo '</tr>';

$i = pg_num_fields($result);
$column=pg_num_fields($result);
$rows = pg_fetch_array($result, NULL, PGSQL_ASSOC);
echo '<form action="upsert.php" method="POST">';
for ($j = 0; $j < $i; $j++) {
	$fieldname = pg_field_name($result, $j);
	echo "<tr>";
	echo "<td>".$fieldname."</td>";
	echo "<td>".$rows[$fieldname]."</td>";
	echo "<td>".pg_field_type($result, $j)."</td>";
	if($j==0){
		$insertsql=$insertsql.$id;
	}else{
		$updatesql=$updatesql.",";
		$insertsql=$insertsql.",";
	}

	if($fieldname==$table."_id"){
		echo '<td>'.$rows[$fieldname]."</td>";
		echo '<input type="hidden" name="'.$fieldname.'" value="'.$rows[$fieldname].'"/>';
	}else{
		echo '<td><input type="text" name="'.$fieldname.'" value="'.$rows[$fieldname].'"/>'."</td>";
	}
	


	echo "</tr>";

	
	if(pg_field_type($result, $j)=="text"){
		$updatesql=$updatesql.$fieldname." = '".$_POST[$fieldname]."'";
		$insertsql=$insertsql."'".$_POST[$fieldname]."'";
	}else{
		$updatesql=$updatesql.$fieldname." = ".$_POST[$fieldname];
		$insertsql=$insertsql.$_POST[$fieldname];
	}
}

echo '<input type="hidden" name="table" value="'.$table.'"/>';
echo '<input type="hidden" name="id" value="'.$id.'"/>';

//echo '<input type="button" value="更新する">';

if($_POST['flag']=='追加準備'){
	echo '<input type="hidden" name="flag" value="追加"/>';
	echo '<input type="submit" name="submit" value="追加する" />';
}else{
	echo '<input type="hidden" name="flag" value="更新"/>';
	echo '<input type="submit" name="submit" value="更新する" />';
}

echo '</form> ';
echo "</table>";


$updatesql=$updatesql." WHERE ".$table."_id = ".$id;
$insertsql=$insertsql.")";


echo '<hr>';
if($_POST['flag']=='更新'){
	echo $updatesql;
	echo '<hr>';
	$result = pg_query($updatesql);
	if (!$result) {
	              die('<br>失敗です。'.pg_last_error());
	}else{
		echo '<br>更新しました';
	}
}
if($_POST['flag']=='追加'){
	echo $insertsql;
	echo '<hr>';
	$result = pg_query($insertsql);
	if (!$result) {
	              die('<br>失敗です。'.pg_last_error());
	}else{
		echo '<br>追加しました';
	}
}


//★★終了

$close_flag = pg_close($link);
if ($close_flag){
//    print('切断に成功しました。<br>');
}
?>

</body>
</html>