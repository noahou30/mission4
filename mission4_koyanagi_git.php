<?php
$dsn='データベース名';
$user='ユーザー名';
$password='パスワード';
$pdo=new PDO($dsn,$user,$password,
array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)
);
?>

<?php
//全データ消去
//$sql="TRUNCATE TABLE keijiban".";";
//$pdo->query($sql);
?>

<?php
//$sql="CREATE TABLE keijiban"
//."("
//."id int auto_increment primary key,"
//."name char(32),"
//."comment TEXT,"
//."datetime datetime,"
//."password char(32)"
//.");";
//$stmt=$pdo->query($sql);
?>

<?php
$name=$_POST["name"];
$comment=$_POST["comment"];
$inputpass=$_POST["inputpass"];
$editpass=$_POST["editpass"];
$delepass=$_POST["delepass"];
$editchoice=$_POST["editchoice"];
$delete=$_POST["delete"];
$editnum=$_POST["editnum"];
?>

<?php
//$sql='SHOW TABLES';
//命令$sqlを$pdoに対してquery、結果を$resultに格納
//$result=$pdo->query($sql);
//foreach($result as $row){
//	echo $row[0];
///	echo '<br>';
//}
//echo"<hr>";
?>

<?php
$sql='SHOW CREATE TABLE keijiban';
$result=$pdo->query($sql);
foreach($result as $row){
	print_r($row);
}
echo "<hr>";
?>

<?php
$sql='SELECT NOW();';
$datetime=$pdo->query($sql);
$datetime=date('Y-m-d H:i:s');

//削除番号が送信された時
if(!empty($delete))
{
	//パスワード未入力
	if(empty($delepass))
	{
		echo "削除用パスワードを入力してください。";
	}
	$sql="SELECT * FROM keijiban";
	$result=$pdo->query($sql);
	foreach($result as $delecon) 
	{
		if($delecon["id"]==$delete && $delecon["password"]==$delepass)
		{
			$sql="DELETE FROM keijiban WHERE id=$delete";
			$stmt=$pdo->query($sql);
		}
	}
}
//編集番号対象選択機能
if(!empty($editchoice))
{
	if(empty($editpass))
	{
		echo "編集用パスワードを入力してください。";
	}
	$sql="SELECT * FROM keijiban";
	$result=$pdo->query($sql);
	foreach($result as $editline){
	    	//投稿番号と一致したものが編集対象行
    	if($editchoice==$editline["id"])
    	{
			if(!empty($editpass) and $editpass !== $editline["password"])
			{
			echo "パスワードが間違っています。";
			}
		//パスワードok
			if($editpass==$editline["password"])
			{
				$data0=$editline["id"];
				$data1=$editline["name"];
				$data2=$editline["comment"];
				}
			}
		}
}

//editnumに値があるとき（編集機能）
if(!empty($editnum)){
	$sql=$pdo->prepare("UPDATE keijiban set name=:name,comment=:comment, datetime=:datetime WHERE id=:id");
	$sql->bindParam(':name',$name,PDO::PARAM_STR);
	$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
	$sql->bindParam(':datetime',$datetime,PDO::PARAM_STR);
	$sql->bindParam(':id',$editnum,PDO::PARAM_INT);
	$sql->execute();
}//if(!empty($editnum))		

//新規投稿
elseif(!empty($name) and !empty($comment) and !empty($inputpass)) {
	//パスワード形式確認
	if(!preg_match('/^(?=.*?[0-9])(?=.*?[a-zA-Z])[0-9a-zA-Z]{6,}$/',$inputpass)){
		echo "パスワードの形式が間違っています。";
	}
	if(preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])[0-9a-zA-Z]{6,}$/',$inputpass)){
		$sql=$pdo->prepare("INSERT INTO keijiban (id,name,comment,datetime,password) VALUES(null,:name,:comment,:datetime,:password)");
		$sql->bindParam(':name',$name,PDO::PARAM_STR);
		$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
		$sql->bindParam(':datetime',$datetime,PDO::PARAM_STR);
		$sql->bindParam(':password',$inputpass,PDO::PARAM_STR);
		$sql->execute();
			}
}
?>

<!DOCTYPE html>
<html lang="ja">
 <head>
 <meta http-equiv="content-type" charset="UTF-8">
 </head>
 <body>
 <form method="POST" action="mission4_koyanagi.php">
	<br />
	<p>パスワードを入力してください。</p>
	<p>6文字以上で、半角英数字をそれぞれ1文字以上含めてください。</p>
	<p> <!--コメント入力-->
	<label>名前<label/><br/>
	<input type="text" name="name" value="<?php echo $data1;?>"/><br />
	<label>コメント<label/><br/>
	<input type="text"  name="comment" value="<?php echo $data2;?>" /><br />
	<input type="hidden" name="editnum" value="<?php echo $data0;?>"/><br/> 
	<input type="password" name="inputpass" placeholder="パスワード"/><br/>
    <input type="submit" value="送信" /><br/>
    <p/>
	<p> <!--コメント編集-->
    <input type="text" name="editchoice" placeholder="編集対象番号"/>
    <input type="password" name="editpass" placeholder="パスワード"/><br/>
    <input type="submit" value="送信"/>
    <p/>
	<p> <!--コメント削除-->
	<input type="text"  name="delete" placeholder="削除対象番号" />
	<input type="password" name="delepass" placeholder="パスワード"/><br/>
	<input type="submit" value="削除"/>
	<p/>
</form>
</body>
</html>

<?php
$sql='SELECT COUNT(*)FROM keijiban';
$stmt=$pdo->query($sql);
$result=$stmt->fetchColumn();
if($result==0)
{
	echo "投稿はまだありません。".'<br>';
}
else{
	echo $result."件の投稿があります。".'<br>';
}
?>

<?php
$sql='SELECT * FROM keijiban';
$stmt=$pdo->query($sql);
$results=$stmt->fetchAll();
foreach($results as $row){
	echo $row['id'].',';
	echo $row['name'].',';
	echo $row['comment'].',';
	echo $row['datetime'].'<br>';
}
?>


<?php
//$sql="delete from keijiban where id=$delete";
//$result=$pdo->query($sql);
?>

