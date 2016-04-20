<?php


$mysqli=false;

// Підєднання БД
function connectDB()
{
	global $mysqli;
	$mysqli = new mysqli("mysql6.000webhost.com", "a9993749_dbase", "dbase4530", "a9993749_dbase");
	$mysqli->query("SET NAMES `utf-8`");
}

// Відєднання БД
function closeDB ()
{
	global $mysqli;
	$mysqli-> close();
}

// Додавання нової команії
function add_new($name, $earnings)
{
	// Перевірка і обробка введеної інформації
	if ($name == "" || $earnings == "")
        show_error("Fill in all fields!");
	else if (!preg_match('/^[0-9a-zA-Zа-яіА-ЯІ_-\s]+$/u', $name))
		show_error("The name contain impermissible characters!");
	else if (!preg_match('/^[0-9.]+$/u', $earnings))
		show_error("The earnings contain impermissible characters!");
	else
    {
	global $mysqli;
	
	connectDB();
	
	// Внесення інформації в БД
	$result = $mysqli->query("INSERT INTO `companies` (`id` ,`name` ,`earnings` ,`total_earnings` ,`parrent` ,`child_count`) VALUES (NULL ,  '".$name."',  '".$earnings."',  '".$earnings."', '0', '0')");
	
	closeDB();
	
	show_message("Company is added!");
	}
}

// Додавання дочрньої компанії
function add_child($name, $earnings, $parrent)
{
	
	// Перевірка і обробка інформації
	 if ($name == "" || $earnings == "")
        show_error("Fill in all fields!");
	else if (!preg_match('/^[0-9a-zA-Zа-яіА-ЯІ_-\s]+$/u', $name))
		show_error("The name contain impermissible characters!");
	else if (!preg_match('/^[0-9.]+$/u', $earnings))
		show_error("The earnings contain impermissible characters!");
	else
    {
	global $mysqli;
	
	connectDB();
	
	// Внесення дочірньої компанії в БД
	$result = $mysqli->query("INSERT INTO `companies` (`id` ,`name` ,`earnings` ,`total_earnings` ,`parrent` ,`child_count`) VALUES (NULL ,  '".$name."',  '".$earnings."',  '".$earnings."', '".$parrent."', '0')");
	
	closeDB();
	
	// Оновлення значеннь батьківських компаній
	incliment_child_count($parrent);
	update_total_earnings($parrent, $earnings);
	
	show_message("Child company is added!");
	}
}

// збільшення лічильника дочірніх компаній
function incliment_child_count($id)
{
	global $mysqli;
	
	connectDB();
	
	$result    = $mysqli->query ("SELECT * FROM companies WHERE `id` = $id");
     
    closeDB();
	
    $company = $result -> fetch_array();
	
	$child_count = $company['child_count'];
	
	connectDB();
	
	$mysqli ->query("UPDATE  `companies` SET  `child_count` =  '".($child_count+1)."' WHERE  `companies`.`id` = $id");
	closeDB;
}

// Збільшення загального прибутку компанії
function update_total_earnings($id, $child_earnings)
{
	global $mysqli;
	
	connectDB();
	
	 $result=$mysqli->query("SELECT * FROM `companies`  WHERE `id` = $id");

	closeDB();	 
	
	$company = $result -> fetch_assoc();
	
	$total_earnings = $company['total_earnings'];
	
	connectDB();
	
	$mysqli ->query("UPDATE  `companies` SET  `total_earnings` =  '".($total_earnings+$child_earnings)."' WHERE  `companies`.`id` = $id");
	closeDB;
	
	// Збільшення загального прибутку всіх батьківських компаній
	if($company['parrent']!='0')
		update_total_earnings($company['parrent'], $child_earnings);
}

// Видалення компанії
function delete_company($id)
{
	global $mysqli;
	
	connectDB();
	
	$result    = $mysqli->query ("SELECT * FROM companies WHERE `id` = $id");

	closeDB();	
	
	$company = $result -> fetch_array();
	$parrent = $company["parrent"];
	$child_count = $company["child_count"];
	
	// Видалення всіх дочірніх компаній
	if($child_count!=0)
		delete_child($company["id"]);
	
	if($parrent!=0)
   { // Зменшення значень в батьківських компаніях
	   decliment_child_count($parrent);	
	   lover_total_earnings($parrent, $company["earnings"]);
   }
   
   connectDB();
   
   $mysqli->query("DELETE FROM `companies`  WHERE `id` = $id");
   
   closeDB();
   
   show_message("Company is deleted!");
   
}

// Видалення дочірніх компаній
function delete_child($parrent)
{
	global $mysqli;
	connectDB();
	
    $result=$mysqli->query("SELECT * FROM `companies`  WHERE `parrent` = $parrent");
  
    closeDB();
	
	$companies = resultToArray($result);
	
	for ($i=0; $i<count($companies); ++$i)
    {
	if($companies[$i]['child_count']!=0) //видалення всіх дочірніх до даної компаній
		delete_child($companies[$i]['id']);
	
	// Зменшення загального прибутку в батьківських компаніях
	lover_total_earnings($companies[$i]['parrent'], $companies[$i]['earnings']);
	
	global $mysqli;
	connectDB();
	
	$mysqli->query("DELETE FROM `companies`  WHERE `id` = '".$companies[$i]['id']."'");
	
	closeDB();
	}
	
	
}

// Зменшення лічильника дочірніх компаній
function decliment_child_count($id)
{
	global $mysqli;
	
	connectDB();
	
	$result    = $mysqli->query ("SELECT * FROM companies WHERE `id` = $id");
	
	closeDB();
	
	$company = $result -> fetch_array();
	
	$child_count = $company['child_count'];
	
	connectDB();
	
	$mysqli ->query("UPDATE  `companies` SET  `child_count` =  '".($child_count-1)."' WHERE  `companies`.`id` = $id");
	
	closeDB;
}

// Зменшення загального прибутку батьківських компаній
function lover_total_earnings($id, $child_earnings)
{
	global $mysqli;
	
	
	echo "</br>";
	connectDB();
	
	 $result=$mysqli->query("SELECT * FROM `companies`  WHERE `id` = $id");

	closeDB();	 
	
	$company = $result -> fetch_assoc();
	
	$total_earnings = $company['total_earnings'];
	
	connectDB();
		
	$mysqli ->query("UPDATE  `companies` SET  `total_earnings` =  '".($total_earnings-$child_earnings)."' WHERE  `companies`.`id` = $id");
	
	closeDB;
	
	if($company['parrent']!=0) // рухаємося вверх по дереву
		lover_total_earnings($company['parrent'], $child_earnings);
}

// видалення всіх компаній
function delete_all()
{
	global $mysqli;
	
	connectDB();
	
	 $mysqli->query("TRUNCATE TABLE `companies`");

	closeDB();	
	
	show_message("All companies are deleted!");
}

// Редагування даних компанії
function edit($new_name, $new_earnings, $id)
{// Перевірка і обробка введених даних
	if ($new_name == "" || $new_earnings == "")
        show_error("Fill in all fields!");
	else if (!preg_match('/^[0-9a-zA-Zа-яіА-ЯІ_-\s]+$/u', $new_name))
		show_error("The name contain impermissible characters!");
	else if (!preg_match('/^[0-9.]+$/u', $new_earnings))
		show_error("The earnings contain impermissible characters!");
	else
    {
	global $mysqli;
	
	connectDB();
	
	$result=$mysqli->query("SELECT * FROM `companies`  WHERE `id` = $id");

	closeDB();	 
	
	$company = $result -> fetch_assoc();
	
	$earnings = $company["earnings"];
	$delta_earnings = $new_earnings - $earnings;
	
	// Оновлення загального прибутку в батьківських компаніях
	update_total_earnings($company["id"], $delta_earnings);
	
	// Оновлення БД
	connectDB();
	$mysqli ->query("UPDATE  `companies` SET  `earnings` =  '".$new_earnings."' , `name` = '".$new_name."' WHERE  `companies`.`id` = $id");
	closeDB();
	
	show_message("Company is edited!");
	}
	
}

// Повертає массив компаній по батьківському id
function get_companies($parrent)
{
	global $mysqli;
	
	connectDB();
    $result=$mysqli->query("SELECT * FROM `companies`  WHERE `parrent` = $parrent");
  
    closeDB();
	
	return resultToArray($result);
}

// Перетворює забит БД в массив
function resultToArray($result)
{
	$array= array();
	while (($row=$result -> fetch_assoc())!=false)
		$array[]=$row;
	
	return $array;
}




// Виводить компанії на екран
function show_companies($parrent)
{
	static $level=0; // рівень занурення дерева компаній
	$level++;
	
	// Генерування стрічки з рисочок (відносно рівня занурення)
	$str="";
	for($i=0; $i<$level; $i++)
		$str=$str."-";
	
	$companies=get_companies($parrent);

for($i=0; $i<count($companies); $i++)
{
	if($_GET["action"]=="edit" && $_GET["id"]==$companies[$i]['id']) // якщо "дія" - редагування, то виводимо форму для введення нових даних
    {
	global $mysqli;
	
	$id = $_GET["id"];
	
	connectDB();
	$result    = $mysqli->query ("SELECT * FROM companies WHERE `id` = $id");
	closeDB();
	
	$company = $result ->fetch_assoc();
		
	echo "<form name='upload' action='?id=".$companies[$i]['id']."' method='POST' ENCTYPE='multipart/form-data' class='add_child'>
		      <label class='str'>$str</label>
	          <input type='text' placeholder='NAME' name='name' id='name' class='edit_name' value='".$company["name"]."'>
	          <input type='text' placeholder='EARNINGS' name='earnings' id='earnings' value='".$company["earnings"]."'>
	          <label>K$</label>
	          <input type='submit' name='edit' id='done' class='formButton' value='Edit!'>
              </form>";
    }
	else // інакше, виводимо дані компанії з отриманого массиву
	{
	echo "<div class='company'>
	<div class='company_inf'>
    ".$str.$companies[$i]['name']." | ".$companies[$i]['earnings']."K$"; echo ($companies[$i]['child_count']!=0) ? " | ".$companies[$i]['total_earnings']."K$": ""; 
    echo "</div>

    <div class='control_buttons'>
    <a href='?action=edit&id=".$companies[$i]['id']."'>Edit</a> | <a href='?action=add_child&id=".$companies[$i]['id']."'>Add Child</a> | <a href='?action=delete&id=".$companies[$i]['id']."'>Delete</a>
    </div>
	</div>";
   }

    if($_GET["action"]=="add_child" && $_GET["id"]==$companies[$i]['id']) //якщо "дія" - додавання дочрньої компанії, то додаємо форму піля батьківсько для вводу дочірньої компанії
   {
	   echo "<form name='upload' action='?parrent=".$companies[$i]['id']."' method='POST' ENCTYPE='multipart/form-data' class='add_child'>
		         <label class='str'>$str-</label>
	             <input type='text' placeholder='NAME' name='name' id='name' class='edit_name'>
	             <input type='text' placeholder='EARNINGS' name='earnings' id='earnings'>
	             <label>K$</label>
	             <input type='submit' name='add_child' id='done' class='formButton' value='Add Child!'>
                 </form>";
   }

    if($companies[$i]['child_count']!=0) // якщо є дочрні команії, то виводимо їх на екран
		show_companies($companies[$i]['id']);
	else echo"<div class='border'> </div>";
}

   $level--; 
}

// Вивід помилки на екран (червони колір тла)
function show_error($str)
{
	   echo "<div class='error'>$str</div>";
}

// Вивід повідомлення на екран (зелений колір тла)
function show_message($str)
{
	   echo "<div class='message'>$str</div>";
}
?>