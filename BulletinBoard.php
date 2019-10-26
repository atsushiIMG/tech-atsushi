<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<?php 
		$dsn='データベース名';
		$user='ユーザー名';	
		$password = 'パスワード';
		$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	//編集投稿番号が入ったらそのIDの行を参照してそれらの名前やコメントをとってくる
		if(!empty($_POST["edit_form"])){
			//あるセルからパスワード持ってきて、それとedit_formが一致していたら
			$id=$_POST["edit_form"];
			$sql="SELECT * FROM bulletin_board_pwmode where id='$id'";
			$stmt=$pdo->query($sql);
			$res=$stmt->fetchAll();
			//出力できる
			foreach ($res as $row){
			//$rowの中にはテーブルのカラム名が入る
				$password=$row['pw'];
				if($password != $_POST["edit_password"]){
					echo "パスワードが違います";
					break;
				}
				else{
					$Num_edit=$row['id'];
					$after_name=$row['name'];
					$after_comment=$row['comment'];
					$after_password=$row['pw'];
				}
			}
			// 出力できない
			// echo $res[0];
			// echo $res[1];
			// echo $res[2];
		}
	 ?>
	<!-- 投稿フォーム -->
	<form method="POST" action="mission_5-1-pwver.php">
		<input type="text" name="output_name" placeholder="名前" value="<?php if(!empty($after_name)){echo $after_name;} ?>">
		<input type="text" name="output_comment" placeholder="コメント" value="<?php if(!empty($after_comment)){echo $after_comment;} ?>">
		<input type="hidden" name="edit_decide" placeholder="編集じゃない" value="<?php if(!empty($Num_edit)){echo $Num_edit;} ?>">
		<input type="text" name="New_password" placeholder="パスワード" value="<?php if(!empty($after_password)){echo $after_password;} ?>">
		<input type="submit" name="送信">
	</form>
	<!-- 削除フォーム -->
	<form method = "POST" action="mission_5-1-pwver.php" >
		<input type="text" name="signNum_delete" placeholder="削除対象">
		<input type="text" name="elim_password" placeholder="パスワード">
		<input type="submit" name="delete">
	</form>
	<!-- 編集フォーム -->
	<form method="POST" action="mission_5-1-pwver.php">
		<input type="text" name="edit_form" placeholder="編集対象">
		<input type="text" name="edit_password" placeholder="パスワード">
		<input type="submit" name="Edit">
	</form>
	<?php 
	// テーブルの作成
	$sql="CREATE TABLE IF NOT EXISTS bulletin_board_pwmode"
	."("
	."id INT AUTO_INCREMENT PRIMARY KEY,"
	."name char(32),"
	."comment TEXT,"
	."Pdate char(32),"
	."pw char(32)"
	.");";
	$stsm=$pdo->query($sql);
	//☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆
	
	//☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆
	// DBへの登録
	if(!empty($_POST["output_name"])&&!empty($_POST["output_name"])&&!empty($_POST["New_password"])){
		if(!empty($_POST["edit_decide"])){
		// テーブルの内容を編集
			$id=$_POST["edit_decide"];
			$name = $_POST["output_name"];
			$comment = $_POST["output_comment"];
			$pw = $_POST["New_password"];
			$Pdate=date("Y/m/d H:i:s");
			$sql = 'update bulletin_board_pwmode set name=:name,comment=:comment,Pdate=:Pdate,pw=:pw where id=:id';
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':name', $name, PDO::PARAM_STR);
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->bindParam(':Pdate', $Pdate, PDO::PARAM_STR);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->bindParam(':pw', $pw, PDO::PARAM_STR);
			$stmt->execute();
		}
		else{
			//新規投稿
			$sql=$pdo->prepare("INSERT INTO bulletin_board_pwmode (name,comment,Pdate,pw) VALUES (:name,:comment,:Pdate,:pw)");
			$name=$_POST["output_name"];
			$sql->bindParam(':name',$name,PDO::PARAM_STR);
			$comment=$_POST["output_comment"];
			$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
			$Pdate=date("Y/m/d H:i:s");
			$sql->bindParam(':Pdate',$Pdate,PDO::PARAM_STR);
			$pw=$_POST["New_password"];
			$sql->bindParam(':pw',$pw,PDO::PARAM_STR);
			$sql->execute();
		}
	}
	//テーブルの詳細（型とか）を見る
	// $sql='SHOW TABLES';
	// $res=$pdo->query($sql);
	// foreach($res as $res_col){
	// 	foreach($res_col as $value){
	// 		echo $value."  ";
	// 	}
	// 	echo '<br>';
	// }
	// echo "<hr>";
	//
	//テーブルの内容を削除する
	if(!empty($_POST["signNum_delete"])&&!empty($_POST[""])){
		$id=$_POST["signNum_delete"];
		$sql="SELECT * FROM bulletin_board_pwmode where id='$id'";
		$stmt=$pdo->query($sql);
		$res=$stmt->fetchAll();
		//出力できる
		foreach ($res as $row){
		//$rowの中にはテーブルのカラム名が入る
			$password=$row['pw'];
			if($password != $_POST["elim_password"]){
				echo "パスワードが違います"."<br>";
				break;
			}
			else{
				//delete from テーブル名　where カラム名=:変数
				$sql='delete from bulletin_board_pwmode where id=:id';
				$stmt=$pdo->prepare($sql);
				$stmt->bindParam(':id',$id,PDO::PARAM_INT);
				$stmt->execute();
			}	
		}
		
	}
	//テーブルの内容を表示
	$sql = 'SELECT * FROM bulletin_board_pwmode';
	$stmt=$pdo->query($sql);
	$res=$stmt->fetchAll();
	foreach ($res as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].' ';
		echo $row['name'].' ';
		echo $row['comment'].'<br>';
	echo "<hr>";
	}
	?>
</body>
</html>