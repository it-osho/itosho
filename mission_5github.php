<!DOCTYPE html> <!- htmlを宣言 ->
<html>
<html lang = “ja”> <!- 言語を指定 ->
<meta charset = "UTF-8"> <!- 文字コードを指定 ->
<body> 

<?php

/*以下内容


データベースへ接続*/
$dsn = 'データベース名'; //ホスト名とデータベース名を指定
$user = 'ユーザー名'; //ユーザー名
$password = 'パスワード'; //パスワード
$pdo = new PDO($dsn, $user, $password, array(PDO:: ATTR_ERRMODE => PDO::ERRMODE_WARNING));
/*↑はMySQLへの接続する手続き。
array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)とは、データベース操作で発生したエラーを
警告として表示してくれる設定をするための要素です。
デフォルトでは、PDOのデータベース操作で発生したエラーは何も表示されません。
その場合、不具合の原因を見つけるのに時間がかかってしまうので、
このオプションはつけておきましょう*/


//テーブルの作成
$sql = "CREATE TABLE IF NOT EXISTS mission5"
."("
."id INT AUTO_INCREMENT PRIMARY KEY,"
."name char(32),"
."comment TEXT,"
."date char(32),"
."pass char(32)" /*常に32文字の長さになるように空白を使って足りない分を埋めて格納されますが、格納されたデータを取得すると文字列の末尾に付いている空白は削除された上で取得されます。*/
.");";
$stmt = $pdo -> query($sql);

	if(!empty($_POST["name"]) and !empty($_POST["comment"]) and empty($_POST["editnum"])){//numberが空の時、新規投稿
		if(empty($_POST["number"])){
		$name = $_POST["name"]; //新規投稿の名前になるところの属性(name="name")をPOST受信して変数に代入
		$comment = $_POST["comment"]; //新規投稿の名前になるところの属性(name="comment")をPOST受信して変数に代入
		$date = date("Y/m/d G:i:s"); //投稿時間獲得
		$pass = $_POST["pass"];

		//ここからinsertを使用してデータをテーブルに代入
		$sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, date, pass) VALUES(:name, :comment, :date, :pass)");
		$sql -> bindParam(':name', $name, PDO::PARAM_STR);
		$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
		$sql -> bindParam(':date', $date, PDO::PARAM_STR);
		$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
		$sql -> execute();
		}else{
		$id = $_POST["number"]; //変更するという投稿番号をPOST受信して、変数に代入
		$sql = 'update mission5 set name = :name, comment = :comment, date = :date where id = :id';
		$stmt = $pdo -> prepare($sql);
		$stmt -> bindParam(':name', $name, PDO::PARAM_STR);
		$stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
		$stmt -> bindParam(':date', $date, PDO::PARAM_STR);
		$stmt -> bindParam(':id', $id, PDO::PARAM_INT); //それぞれ取得
		$stmt -> execute(); //遂行
		}
	}

	//ここから削除機能
	if(!empty($_POST["deletenum"])and empty($_POST["edit"])){ //もしPOST受信した削除対象番号が空でなく、削除ボタンを受け取った場合
	$deletenum = $_POST["deletenum"];
	$deletepass = $_POST["deletepass"];
	$sql = 'SELECT * FROM mission5'; //表示
	$stmt = $pdo -> query($sql); //引数に指定したSQL文をデータベースに対して発行してくれます。
	$result = $stmt -> fetchAll(); //結果データすべてを配列で取得
		foreach ($result as $ele){
			if($ele['id'] == $deletenum){
				if($ele["pass"] == $deletepass){
				$id = $deletenum;
				$sql = 'delete from mission5 where id = :id';
				$stmt = $pdo -> prepare($sql);
				$stmt -> bindParam(':id', $id, PDO::PARAM_INT);
				$stmt -> execute();
				}
			}
		}
	}
	
	//ここから編集機能
	if(!empty($_POST["editnum"]) and empty($_POST["deletnum"])){
	$editnum = $_POST["editnum"]; //ここから
	$editpass = $_POST["editpass"];
	$sql = 'SELECT * FROM mission5';
	$stmt = $pdo -> query($sql);
	$result = $stmt -> fetchAll(); //ここまでが配列格納処理
		foreach($result as $ele){
			if($ele['id'] == $editnum){
				if($ele['pass'] == $editpass){
				$editname = $ele['name'];
				$editcomment = $ele['comment']; //編集後の名前・コメントを取得
				}
			}
		}
	}
			


?>
<p>
クリスマスプレゼントで欲しい物は何ですか？
</p>

<!- 送信・削除・編集フォーム ->
	<form  method = "post"> <!- 送受信方法を指定 ->
	<p><input type = "text" name = "name"  value = "<?php 
							if(!empty($_POST["editnum"])){
								if($ele['pass'] == $_POST["editpass"]){
								echo $editname;
								}
							}?>" placeholder = "名前"></p>
	<p><input type = "text" name = "comment" value = "<?php
							if(!empty($_POST["editnum"])){
								if($ele['pass'] == $_POST["editpass"]){
								echo $editcomment;
								}
							}?>" placeholder = "コメント"></p>
	<p><input type = "text" name = "pass" value = "" placeholder = "パスワード"></p>
	<p><input type = "submit" name = "submit" value = "送信"></p>
	<p><input type = "hidden" name = "number" value = "<?php
							if(!empty($_POST["editnum"])){
							echo $editnum;
							} ?>" ></p>

	<p><input type = "text" name = "deletenum" value = "" placeholder = "削除対象番号"></p>
	<p><input type = "text" name = "deletepass" value = "" placeholder = "パスワード"></p>
	<p><input type = "submit" name = "delete" value = "削除"></p>

	<p><input type = "text" name = "editnum" value = "" placeholder = "編集対象番号"></p>
	<p><input type = "text" name = "editpass" value = "" placeholder = "パスワード"></p>
	<p><input type = "submit" name = "edit" value = "編集"></p>
	
<br>
<font size = "5"><b>クリスマスプレゼントは何？</b></font>
<hr> <!-線を引く ->

<?php //表示機能
$sql = 'SELECT * FROM mission5';
$stmt = $pdo -> query($sql);
$result = $stmt -> fetchAll();//fetchは1件しかデータを取得しませんでしたが、fetchAllは結果データを全件まとめて配列で取得します。*/
foreach($result as $ele){ //$rowの中にはテーブルのカラム名が入る
echo $ele['id'].':';
echo $ele['name'].'<br>';
echo $ele['comment'].'　　　　　' .$ele['date'].'<br>';
echo '<hr>';
}
?>
</body>
</html>