<head>
<title>COMPANIES</title>
<meta charset="utf-8">

<link rel="stylesheet" type="text/css" href="css/style.css">

<?php
require_once "functions.php";
?>
</head>

<body>


<div id="content">
<?php

// Додавання нової компанії
if(isset($_POST["add_new"]))
{
$name=htmlspecialchars($_POST["name"]);
$earnings=str_replace(",",".",htmlspecialchars($_POST["earnings"]));
    
		add_new($name, $earnings);
		
}

// Додавання дочірньої компанії
if(isset($_POST["add_child"]))
{
$name=htmlspecialchars($_POST["name"]);
$earnings=str_replace(",",".",htmlspecialchars($_POST["earnings"]));
$parrent=htmlspecialchars($_GET["parrent"]);
   
		add_child($name, $earnings, $parrent);
		
}

// Редагування компанії
if(isset($_POST["edit"]))
{
$new_name=htmlspecialchars($_POST["name"]);
$new_earnings=str_replace(",",".",htmlspecialchars($_POST["earnings"]));
$id=$_GET["id"];
   
		edit($new_name, $new_earnings, $id);
		
}


// Видалення компанії
if($_GET["action"]=="delete")
	delete_company($_GET["id"]);

// Видалення всіх компаній
if($_GET["action"]=="delete_all")
	delete_all();
?>

<div class="companies">
<div class="company">
<div id="table_head">Name | Company Estimated Earnings | Company Estimated Earnings + Child Company Estimated Earnings</div> 
</div>
  <?php
  // Вивід таблиці компаній на екран
  show_companies(0);
  ?>

</div>

<!-- Форма додавання нової компанії-->
<form name="upload" action="?action='add_new'" method="POST" ENCTYPE="multipart/form-data" class='add_new'>
	<input type="text" placeholder="NAME" name="name" id="name">
	<input type="text" placeholder="EARNINGS" name="earnings" id="earnings">
	<label>K$</label>
	<input type="submit" name="add_new" id="done" class="formButton" value="Add New!">
</form>

<!-- Кнопка "видалити всі"-->
<div id="delete_all">
<a href='?action=delete_all'>---Delete All Companies---</a>	
</div>

<!-- Кнопка "оновити"-->
<div id="refresh">
<a href='?action=refresh'>---Refresh---</a>
</div>

</div>


</body>