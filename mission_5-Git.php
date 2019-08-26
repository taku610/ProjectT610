<?php
//データベース接続
$dsn = 'mysql:dbname=データベース名db;host=localhost';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

if($pdo !== false){
	}else{
	echo "データベースに接続できませんでした。";
	}

//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS board"
	. "("
	. "id INT not null AUTO_INCREMENT PRIMARY KEY,"
	. "name varchar(32),"
	. "comment TEXT,"
	. "time varchar(32),"
	. "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);

//全体で使う変数定義
$now = date("Y/m/d/ H:i:s");

//編集選択変数定義
$editnumber = NULL;
$editname = NULL;
$editcomment = NULL;

//編集選択機能
if (isset($_POST["edit"])){
  $edit = $_POST["edit"];
  $editpass = $_POST["editpass"];
	if ($edit !== "" && $editpass !== ""){
	$sql = 'SELECT * FROM board';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
		foreach ($results as $row){
			if ($row['id'] == $edit){
			if ($row['pass'] == $editpass){
			  $editnumber = $row['id'];
			  $editname = $row['name'];
			  $editcomment = $row['comment'];
			}
			}
		}
	}
}

//新規投稿機能と編集機能
if (!empty($_POST)){
  $name = $_POST["name"];
  $comment = $_POST["comment"];
  $pass = $_POST["pass"];
//編集機能
	if (isset($_POST["number"])){
	  $number = $_POST["number"];
		if($number !== ""){
		  $sql = 'SELECT * FROM board';
		  $stmt = $pdo->query($sql);
		  $results = $stmt->fetchAll();
			foreach ($results as $row){
				if ($row['id'] == $number){
					if ($pass !== ""){
					  $id = $number; //変更する投稿番号
					  $sql = 'update board set name=:name,comment=:comment,time=:time,pass=:pass where id=:id';
					  $stmt = $pdo->prepare($sql);
					  $stmt->bindParam(':name', $name, PDO::PARAM_STR);
					  $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
					  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
					  $stmt->bindParam(':time', $now, PDO::PARAM_STR);
					  $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
					  $stmt->execute();
					}
				}
			}
		}
//新規投稿機能
		elseif ($comment !== "" &&$name !== ""){
			if ($pass !== ""){
			  $sql = $pdo -> prepare("INSERT INTO board (name, comment, time, pass) VALUES (:name, :comment, :time, :pass)");
			  $sql -> bindParam(':name', $name, PDO::PARAM_STR);
			  $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
			  $sql -> bindParam(':time', $now, PDO::PARAM_STR);
			  $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
			  $sql -> execute();
			}
		}
	 }
}

//削除機能
if (isset($_POST["delete"])){
  $delete = $_POST["delete"];
  $delpass = $_POST["delpass"];
	if($delete !== ""){
	  $sql = 'SELECT * FROM board';
	  $stmt = $pdo->query($sql);
	  $results = $stmt->fetchAll();
		foreach ($results as $row){
			if ($row['id'] == $delete){
			if ($row['pass'] == $delpass){
			  $id = $delete;
			  $sql = 'delete from board where id=:id';
			  $stmt = $pdo->prepare($sql);
			  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
			  $stmt->execute();
			}
			}
		}
	}
}
//フォーム
?>

<html>
  <head>
    <meta charset="UTF-8">
  </head>
  <body>

    <form action="" method="POST">
	＜投稿フォーム＞
	<p>名前：未入力送信不可('ω')<br>
	<input type="text" name="name" size="40" 
	value="<?php echo $editname; ?>"></p>
	<p>コメント：未入力送信不可(´･ω･`)<br>
	<input type="text" name="comment" size="40"
	value="<?php echo $editcomment; ?>"></p>
	<p>パスワード：新しいパスワード入力が必須です<br>
	<input type="text" name="pass" size="40"
	value=""></p>
	<p><input type="submit" value="投稿"></p><br>
	<!--＜編集投稿番号＞-->
	<p><input type ="hidden" name="number" size="40"
	value="<?php echo $editnumber; ?>"></p>

	＜削除フォーム＞
	<p>削除したい投稿の番号を半角数字で指定してください(><)<br>
	<input type="text" name="delete"></p>
	<p>パスワード：入力しないと削除できません<br>
	<input type="text" name="delpass" size="40"
	value=""></p>
	<p><input type="submit" value="削除"></p><br>

	＜編集フォーム＞
	<p>編集したい投稿の番号を半角数字で指定してください(><)<br>
	<input type="text" name="edit"></p>
	<p>パスワード：入力しないと編集できません<br>
	<input type="text" name="editpass" size="40"
	value=""></p>
	<p><input type="submit" value="編集"></p>
    </form>
  </body>
</html>

<?php
//データ表示
$sql = 'SELECT * FROM board';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
  echo $row['id'].',';
  echo $row['name'].',';
  echo $row['comment'].',';
  echo $row['time'].'<br>';
  echo "<hr>";
}
