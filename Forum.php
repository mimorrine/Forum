<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<html lang="ja">
<title>ミモリの掲示板</title>

<style type="text/css">
.title:first-letter { //タイトルデザイン-> 1文字目を色付きで大きく表示
  font-size: 2em;
  color: #330066;
  line-height: 0.75em
}
.box{ //見出しデザイン->　二重線の枠
	background: none;
	border: 1px solid #330066;	
	margin-left: 30px; 
	padding: 20px; 
	position: relative;
}
.box:after{
	background: none;
	border: 1px solid #330066;
	content: '';
	position: absolute;
	top: 3px;
	left: 3px;
    width: 100%;
	height: 100%;
	z-index: -1;
}
.btn { //送信ボタンデザイン-> 紫色の四角いハコ
	display: block;
	position: relative;
	padding: 0.25em 0.5em;
    margin-top: 10px;
    font-size: medium;
    font-weight: bold;
	text-align: center;
	text-decoration: none;
	color: #fff;
	background: #330066;
    transition: .4s;
}
.btn:hover { //ボタンにカーソルを乗せるとピンク色に
	 cursor: pointer;
	 text-decoration: none;
	 background:#cc0066;
}
.line { //ラインを点線に
	border-width: 1px 0 0 0;
	border-style: dashed;
	border-color: #330066;
}
</style>
</head>
<body>

<?php
//データベース接続
$dsn = データベース名;
$user = ユーザー名;
$password = パスワード;
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS forumdb"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32) not null,"
    . "comment TEXT not null,"
    . "date TEXT,"
	. "pass varchar(20) not null"
	.");";
    $stmt = $pdo->query($sql);

//名前とテキストが入力されているとき
if(!empty($_POST["name"]) && !empty($_POST["txt"]) && !empty($_POST["pass"])){ 
    if(empty($_POST["edinum"])){ //編集番号が入力されていないとき 
        $sql = $pdo -> prepare("INSERT INTO forumdb (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);

        $name = $_POST["name"];
        $comment = $_POST["txt"]; 
        $date = date("Y/m/d h:i:s");
        $pass = $_POST["pass"];
        $sql -> execute();

    }else{ //編集番号が入力されているとき
      $sql = 'SELECT * FROM forumdb';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach ($results as $row) {
        if($_POST["edinum"]==$row['id']){
            $id = $_POST["edinum"];
                $name = $_POST["name"];
                $comment = $_POST["txt"]; 
                $date = date("Y/m/d h:i:s");
                $pass = $_POST["pass"];
                $sql = 'UPDATE forumdb SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';

                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
        }
    }
}
}

//削除番号が入力されているとき
if(!empty($_POST["dsub"]) && !empty($_POST["dpass"])){
    $sql = 'SELECT * FROM forumdb';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        if($_POST["delete"]==$row['id'] && $_POST["dpass"]==$row['pass']){
            $id = $_POST["delete"];
            $sql = 'delete from forumdb where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
}
//編集モード
if(!empty($_POST["edit"]) && !empty($_POST["epass"])){
    $sql = 'SELECT * FROM forumdb';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        if($_POST["edit"]==$row['id'] && $_POST["epass"]==$row['pass']){
            //フォームのお名前とコメント（id,nameとtxt）に編集対象の内容を渡す
            $enum=$row['id'];
            $en=$row['name'];
            $ei=$row['comment'];
        }
    }
}
?>

<h2 class="title">What Do You Recommend?</h2>
<p class="box">あなたのオススメする○○をおしえてね(^o^)<br>食べ物、音楽、本、漫画、ゲーム、ヒト、お店etc... 何でもOK！<p>

    <form action="" method="post">
        <input type="hidden" name="edinum" value="<?php if(!empty($enum)){ echo $enum;} ?>">  //表示されない
        <input type="text" name="name" value="<?php if(!empty($en)){ echo $en;}?>" placeholder="お名前" ><br>
        <input type="text" name="txt" value="<?php if(!empty($ei)){ echo $ei;}?>" placeholder="コメント" ><br>
        <input type="text" name="pass" placeholder="パスワード"><br>
        <input type="submit" name="submit" value="> Submit" class="btn"><br>
    </form>

    <form action="" method="post">
        <input type="text" name="delete" placeholder="削除対象番号"><br>
        <input type="text" name="dpass" placeholder="パスワード"><br>
        <input type="submit" name="dsub" value="> Delete" class="btn"><br>
    </form>

    <form action="" method="post">
        <input type="text" name="edit" placeholder="編集対象番号"><br>
        <input type="text" name="epass" placeholder="パスワード"><br>
        <input type="submit" name="esub" value="> Edit" class="btn"><br>
    </form>

<h3 class="title">みんなのオススメ！</h3>
<?php
//表示   
$sql = 'SELECT * FROM forumdb';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'].' | ';
    echo $row['name'].' | ';
    echo $row['comment'].' | ';
    echo $row['date'].'<br>';
echo '<hr class="line">';
}
?>
<br>
<br>

</body>
</html>