<?php
define(POP_SIZE,10);
define(M_RATE, 0.02);
define(LARGE, 100000);
define(TIMES, 150);

$dsn = 'サーバー名';
$user='ユーザー名';
define(PASS,'パスワード');

function mat_mult($mat1, $mat2)
{
	$lhs_row = count($mat1);
	$lhs_col = count($mat1[0]);
	$rhs_row = count($mat2);
	$rhs_col = count($mat2[0]);

	if($lhs_col === $rhs_row)
	{
		if($lhs_row === 1)
		{
			if(!($rhs_col === 1))
			{
				$result = array(array());
				for($i = 0; $i < $rhs_col; $i++)
				{
					for($j = 0; $j < $rhs_row; $j++)
					{
						$result[0][$i] += $mat1[0][$j] * $mat2[$j][$i];
					}
				}
			}
			else if($rhs_col === 1)
			{
				for($i = 0; $i < $lhs_col; $i++)
				{
					$result += $mat1[0][$i] * $mat2[$i];
				}
			}
		}
		else if($rhs_col === 1)
		{
			$result = array();
			for($i = 0; $i < $lhs_row; $i++)
			{
				for($j = 0; $j < $lhs_col; $j++)
				{
					$result[$i] += $mat1[$i][$j] * $mat2[$j];
				}
			}
		}
		
		else
		{
			$result = array(array());
			for($i = 0; $i < $lhs_row; $i++)
			{
				for($j = 0; $j < $rhs_col; $j++)
				{
					for($k = 0; $k < $rhs_row; $k++)
					{
						$result[$i][$j] += $mat1[$i][$k] * $mat2[$k][$j];
					}
				}
			}
		}
	}
	else echo 'error these matrixes can not multiply'.'</br>';
	
	return $result;
}

function mat_trans($mat)
{
	$row = count($mat);
	$col = count($mat[0]);
	$result = array(array());

	if($col === 1)
	{
		for($i = 0; $i < $row; $i++)
		{
			$result[0][$i] = $mat[$i];
		}
	}

	else
	{
		for($i = 0; $i < $row; $i++)
		{
			for($j = 0; $j < $col; $j++)
			{
				$result[$j][$i] = $mat[$i][$j];
			}
		}
	}
	
	return $result;
}

function quick_sort($array)
{
	// find array size
	$length = count($array);
	
	// base case test, if array of length 0 then just return array to caller
	if($length <= 1){
		return $array;
	}
	else{
	
		// select an item to act as our pivot point, since list is unsorted first position is easiest
		$pivot = $array[0];
		
		// declare our two arrays to act as partitions
		$left = $right = array();
		
		// loop and compare each item in the array to the pivot value, place item in appropriate partition
		for($i = 1; $i < count($array); $i++)
		{
			if($array[$i] < $pivot){
				$left[] = $array[$i];
			}
			else{
				$right[] = $array[$i];
			}
		}
		
		// use recursion to now sort the left and right lists
		return array_merge(quick_sort($left), array($pivot), quick_sort($right));
	}
}

function calc_heuristic($gene, $cook, $nutrition, $value_cook, $limit_nutrition, $max_money, $max_nutrition)//引数(遺伝子/料理行列/料理ごとの栄養素行列/材料ごとの価格/摂取制限量/最大コスト/摂取目標)
{
	$X = array(array());
	$X = mat_mult(mat_trans($gene), $cook);

	$a = 1;
	$N = array(array());
	$N = mat_mult($X, $nutrition);
	for($i = 0; $i < count($N); $i++)
	{
		if($N[0][$i] > $limit_nutrition[$i])
		{
			$a = 0;
		}
	}

	$b = 1;
	$value = mat_mult($X, $value_cook);
	if($value > $max_money)
	{
		$b = 0;
	}
	
	$result = 0;
	for($i = 0; $i < count($N[0]); $i++)
	{
		if($i < 22)
		{
			if($N[0][$i] > $max_nutrition[$i]) $temp1 = 1;
			else $temp1 = $N[0][$i] / $max_nutrition[$i];

			if($N[0][$i + 1] > $max_nutrition[$i + 1]) $temp2 = 1;
			else $temp2 = $N[0][$i + 1] / $max_nutrition[$i + 1];
			
			$result += ($temp1) * ($temp2);
		}
		else if($i ===22)
		{

			if($N[0][$i] > $max_nutrition[$i]) $temp1 = 1;
			else $temp1 = $N[0][$i] / $max_nutrition[$i];

			if($N[0][0] > $max_nutrition[0]) $temp2 = 1;
			else $temp2 = $N[0][0] / $max_nutrition[0];
			
			$result += ($temp1) * ($temp2);
		}
		else if($i > 22) echo "size error heuristic function is wrong.";
	}
	
	return ($result * $a * $b * sin(pi() / 23)) / 2;
}

function swap(&$rhs, &$lhs)
{
	$temp = $rhs;
	$rhs = $lhs;
	$lhs = $temp;
}

function two_point_crossover_double_return($gene1, $gene2, $num = 100)//返り値配列、list($c_gene1, $c_gene2)で受け取る
{
	$size = count($gene1);
	$point1 = mt_rand() % $size;
	$point2 = mt_rand() % $size;

	$temp = 0;
	if($point1 > $point2)
	{
		swap($point1, $point2);
	}
//	echo '交叉ポイント = '. $point1.', '.$point2.'<br/>';
	if($point2 - $point1 > $size/2)
	{
		for($i = 0; $i < $point1 ; $i++)
		{
			swap($gene1[$i], $gene2[$i]);
		}
		for($i = $point2; $i < $size; $i++)
		{
			swap($gene1[$i], $gene2[$i]);
		}
	}
	else
	{
		for($i = $point1; $i < $point2; $i++)
		{
			swap($gene1[$i], $gene2[$i]);
		}
	}
	
	$rand_point = mt_rand() % $size;
	$count1 = 0;
	$count2 = 0;
	for($i = $rand_point, $j = 0; $j <$size; $i++, $j++)
	{
		if($gene1[$i % $size] === 1)
		{
			$count1++;
			if($count1 > $num) $gene1[$i % $size] = 0;
		}
		if($gene2[$i % $size] === 1)
		{
			$count2++;
			if($count2 > $num) $gene2[$i % $size] = 0;
		}
	}
	return array($gene1, $gene2);
}

function two_selection_double_return($mat)
{
	$size = count($mat);
	$i = mt_rand() % $size;
	$j = mt_rand() % $size;
	while($i === $j)
	{
		$j = mt_rand() % $size;
	}
	
	return array($mat[$i], $mat[$j], $i, $j);
}

function roulette($arr)//一次元配列を引数に受け取り、確率的に配列の位置を決定し返す。(返り値　整数)
{
	$size = count($arr);
	$sum = 0;
	for($i = 0; $i < $size; $i++)
	{
		$sum += $arr[$i];
	}
	if(!$sum) $sum = 1;
	$sorted_arr = quick_sort($arr);
	$arrow = mt_rand(0, 100) / 100;
	$total = 0;
	$result = 0;
	for($i = 0; $i < $size; $i++)
	{
		$total += ($sorted_arr[$i] / $sum);
		if($total >= $arrow)
		{
			$result = array_search($sorted_arr[$i], $arr);
		}
	}

	return $result;
}

function MGG($p_mat, $cook, $nutrition, $value_cook, $limit_nutrition, $max_money, $max_nutrition, $num = 100)
{
	$father = array();
	$mother = array();
	list($father, $mother, $row1, $row2) = two_selection_double_return($p_mat);

	$c_mat = array(array());
	for($i = 0; $i < POP_SIZE - 1; $i = $i + 2)
	{
		list($c_mat[$i], $c_mat[$i + 1]) = two_point_crossover_double_return($father, $mother, $num);
	}
/*	echo '-----------子孫遺伝子----------'.'</br>';
	show_gene($c_mat);
	echo '-------------------------------'.'</br>';
*/
	$heuristic = array();
	for($i = 0; $i < POP_SIZE; $i++)
	{
		$heuristic[$i] = calc_heuristic($c_mat[$i], $cook, $nutrition, $value_cook, $limit_nutrition, $max_money, $max_nutrition);
	//	echo '評価値['.$i.'] = '.$heuristic[$i].'</br>';
	}

	$sorted_heuristic = quick_sort($heuristic);
	$select1 = array_search($sorted_heuristic[POP_SIZE - 1], $heuristic);
	$temp = $heuristic[$select1];
	$heuristic[$select1] = 0;
	$select2 = roulette($heuristic);
	$heuristic[$select1] = $temp;
	return array($c_mat[$select1], $c_mat[$select2], $row1, $row2);
}

function mutation(&$gene)
{
	$size = count($gene);
	$i = mt_rand() % $size;
	swap($gene[$i], $gene[($i + 1) % $size]);
}

function make_gene($length, $num)
{
	$temp = $num;
	$gene = array(array());
	$n = array();
	for($i = 0; $i < POP_SIZE; $i++)
	{
		for($j = 0; $j < $length; $j++)
		{
			if($temp > 0) $gene[$i][$j] = 1;
			else $gene[$i][$j] = 0;
			$temp--;
		}
		$temp = $num;
		shuffle($gene[$i]);
	}

	return $gene;
}

function show_gene($gene)
{
	$row = count($gene);
	$col = count($gene[0]);
	for($i = 0; $i < $row; $i++)
	{
		echo '第'.$i.'番目の遺伝子＝[';
		for($j = 0; $j < $col; $j++)
		{
			if($j < $col - 1) echo $gene[$i][$j].',';
			else echo $gene[$i][$j];
		}
		echo ']'.'</br>';
	}
}
/*
///////////TEST///////////
$food1 = array(1, 1, 1, 1, 1);
$food2 = array(2, 2, 2, 2, 2);
$food3 = array(3, 3, 3, 3, 3);
$food4 = array(4, 4, 4, 4, 4);
$food5 = array(5, 5, 5, 5, 5);

$cook = array($food1, $food2, $food3, $food4, $food5);
$gene = make_gene(5, 3);

echo '-----------初期遺伝子----------'.'</br>';
show_gene($gene);
echo '-------------------------------'.'</br>';

$food1_nut = array(10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10);
$food2_nut = array(20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20);
$food3_nut = array(30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30);
$food4_nut = array(40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40, 40);
$food5_nut = array(50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50);

$nutrition = array($food1_nut, $food2_nut, $food3_nut, $food4_nut, $food5_nut);

$value_cook = array(100, 50, 120, 150, 70);

$limit_nutrition = array(3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000, 3000);
$max_money = 3000;
$max_nutrition  = array(180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180, 180);



//評価関数テスト
$h = calc_heuristic($gene[0], $cook, $nutrition, $value_cook, $limit_nutrition, $max_money, $max_nutrition);
echo '遺伝子[0]の評価値 = '.$h.'</br>';



//クイックソートテスト
$rand_arr = array(mt_rand(0,10), mt_rand(0,10), mt_rand(0,10), mt_rand(0,10), mt_rand(0,10));
echo 'ソート前 = ';
foreach($rand_arr as $i)
{
	echo $i.'  ';
}
echo '</br>';
$sorted_arr = array();
$sorted_arr = quick_sort($rand_arr);
echo 'ソート後 = ';
foreach($sorted_arr as $i)
{
	echo $i.'  ';
}
echo '</br>';



//交叉テスト
$c_gene1 = array();
$c_gene2 = array();

echo '-----------交叉遺伝子----------'.'</br>';
list($c_gene1, $c_gene2) = two_point_crossover_double_return($gene[0], $gene[1]);
echo '0 = 親1, 1 = 親2, 2 = 子1, 3 = 子2</br>';
show_gene(array($gene[0], $gene[1], $c_gene1, $c_gene2));
echo '-------------------------------'.'</br>';



//ルーレットテスト
$arr = array(0, 1, 0, 0, 0);
$i = roulette($arr);
echo 'ルーレットで選ばれた位置 = '.$i.'値 = '.$arr[$i].'</br>';



//ランダム2遺伝子選択
echo '-----------乱択遺伝子----------'.'</br>';
show_gene(two_selection_double_return($gene));
echo '-------------------------------'.'</br>';



//MGGテスト
echo '-----------選択遺伝子----------'.'</br>';
show_gene(MGG($gene, $cook, $nutrition, $value_cook, $limit_nutrition, $max_money, $max_nutrition));
echo '-------------------------------'.'</br>';



//突然変異テスト
echo '突然変異前 = ';
foreach($gene[0] as $i)
{
	echo $i.' ';
}
echo '</br>';
mutation(&$gene[0]);
echo '突然変異後 = ';
foreach($gene[0] as $i)
{
	echo $i.' ';
}
echo '</br>';
*/

$num = $_POST['num'];
$money = $_POST['money'];
$limit_nutritions = array(LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE, LARGE);
$limit_nutritions[$_POST['limit_nutrition_name1']] = $_POST['limit_nutrition_num1'];
$limit_nutritions[$_POST['limit_nutrition_name2']] = $_POST['limit_nutrition_num2'];
$limit_nutritions[$_POST['limit_nutrition_name3']] = $_POST['limit_nutrition_num3'];
/*
echo '---------------HTMLからの受取チェック-------------------</br>';
	echo "品数 = $num, 合計金額 = $money".'</br>';
	echo '摂取目標</br>';
	foreach($limit_nutritions as $row)
	{
		echo $row.'</br>';
	}
echo '---------------HTMLからの受取チェック-------------------</br>';
*/

try
{
	$pdo=new PDO
		(
			$dsn, $user, PASS,
			array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        			PDO::ATTR_EMULATE_PREPARES => false//動的プレースホルダ使用禁止
			     )
		);
	$cooks_name = array(array());
	$table_name = 'cook';
	$sql="SELECT * FROM $table_name";
	$cooks_name = $pdo -> query($sql) -> fetchAll(PDO::FETCH_NUM);
	for($i = 0; $i < count($cooks_name); $i++)
	{
		for($j = 2; $j < count($cooks_name[0]); $j++)
		{
			$cooks[$i][$j - 2] = $cooks_name[$i][$j];
		}
	}
/*	echo '---------------cooks配列-------------------</br>';
	foreach($cooks as $row)
	{
		print_r($row);
		echo '</br>';
	}
	echo '---------------cooks配列-------------------</br>';
*/	
	$products = array(array());
	$temp = array(array());
	$table_name = 'products';
	$sql="SELECT * FROM $table_name";
	$temp = $pdo -> query($sql) -> fetchAll(PDO::FETCH_NUM);
	for($i = 0; $i < count($temp); $i++)
	{
		for($j = 1; $j < count($temp[0]); $j++)
		{
			$products[$i][$j - 1] = $temp[$i][$j];
		}
	}
/*	echo '---------------products配列-------------------</br>';
	foreach($products as $row)
	{
		print_r($row);
		echo '</br>';
	}
	echo '---------------products配列-------------------</br>';
*/
	$max_nutritions = array();
	$table_name = 'limit_nutrition';
	$sql="SELECT * FROM $table_name";
	$max_nutritions = $pdo -> query($sql) -> fetch(PDO::FETCH_NUM);
/*	echo '---------------max_nutritions配列-------------------</br>';
		print_r($max_nutritions);
		echo '</br>';
	echo '---------------max_nutritions配列-------------------</br>';
*/
	$value_products = array();
	$table_name = 'cost';
	$sql="SELECT * FROM $table_name";
	$value_products = $pdo -> query($sql) -> fetch(PDO::FETCH_NUM);
/*	echo '---------------value_products配列-------------------</br>';
		print_r($value_products);
		echo '</br>';
	echo '---------------value_products配列-------------------</br>';
*/
}
catch(PDOException $e)
{
	echo 'パスワードが間違っています<br/>';
	$error=$e->getMessage();
}


////////////計算処理//////////////
$p_gene = make_gene(count($cooks), $num);
/*
echo '--------0回目---------</br>';
show_gene($p_gene);
for($i = 0; $i < POP_SIZE; $i++)
{
	$h = calc_heuristic($p_gene[$i], $cooks, $products, $value_products, $limit_nutritions, $money, $max_nutritions);
	echo "第 $i 番目遺伝子の評価値 = ".$h.'</br>';
}
*/
$c_gene = array(array());
$count = 0;
while($count < TIMES)
{
	list($c_gene[0], $c_gene[1], $row1, $row2) = MGG($p_gene, $cooks, $products, $value_products, $limit_nutritions, $money, $max_nutritions, $num);
	$p_gene[$row1] = $c_gene[0];
	$p_gene[$row2] = $c_gene[1];
	for($i = 0; $i < POP_SIZE; $i++)
	{
		if(mt_rand(0, 100) / 100 < M_RATE) mutation(&$p_gene[$i]);
	}
/*
	if($count === 50)
	{
		echo '--------50回目---------</br>';
		show_gene($p_gene);
		for($i = 0; $i < POP_SIZE; $i++)
		{
			$h = calc_heuristic($p_gene[$i], $cooks, $products, $value_products, $limit_nutritions, $money, $max_nutritions);
			echo "第 $i 番目遺伝子の評価値 = ".$h.'</br>';
		}
	}
	if($count === 100)
	{
		echo '--------100回目---------</br>';
		show_gene($p_gene);
		for($i = 0; $i < POP_SIZE; $i++)
		{
			$h = calc_heuristic($p_gene[$i], $cooks, $products, $value_products, $limit_nutritions, $money, $max_nutritions);
			echo "第 $i 番目遺伝子の評価値 = ".$h.'</br>';
		}
	}
*/
	$count++;
}
/*
echo '--------150回目---------</br>';
show_gene($p_gene);
*/
$h = array();
for($i = 0; $i < POP_SIZE; $i++)
{
	$h[$i] = calc_heuristic($p_gene[$i], $cooks, $products, $value_products, $limit_nutritions, $money, $max_nutritions);
//	echo "第 $i 番目遺伝子の評価値 = ".$h[$i].'</br>';
}
$sorted_h = array();
$sorted_h = quick_sort($h);
$select = array_search($sorted_h[POP_SIZE - 1], $h);

$X = array(array());
$X = mat_mult(mat_trans($p_gene[$select]), $cooks);

$N = array(array());
$N = mat_mult($X, $products);

$nut1 = $N[0][0] /  $max_nutritions[0];
//echo $nut1.'</br>';
if($nut1 > 1) $nut1 = 1;
$nut2 = $N[0][1] /  $max_nutritions[1];
//echo $nut2.'</br>';
if($nut2 > 1) $nut2 = 1;
$nut3 = $N[0][2] /  $max_nutritions[2];
//echo $nut3.'</br>';
if($nut3 > 1) $nut3 = 1;
$nut4 = $N[0][3] /  $max_nutritions[3];
//echo $nut4.'</br>';
if($nut4 > 1) $nut4 = 1;
$nut5 = $N[0][4] /  $max_nutritions[4];
//echo $nut5.'</br>';
if($nut5 > 1) $nut5 = 1;
$nut6 = $N[0][5] /  $max_nutritions[5];
//echo $nut6.'</br>';
if($nut6 > 1) $nut6 = 1;
$nut7 = $N[0][6] /  $max_nutritions[6];
//echo $nut7.'</br>';
if($nut7 > 1) $nut7 = 1;
$nut8 = $N[0][7] /  $max_nutritions[7];
//echo $nut8.'</br>';
if($nut8 > 1) $nut8 = 1;
$nut9 = $N[0][8] /  $max_nutritions[8];
//echo $nut9.'</br>';
if($nut9 > 1) $nut9 = 1;
$nut10 = $N[0][9] /  $max_nutritions[9];
//echo $nut10.'</br>';
if($nut10 > 1) $nut10 = 1;
$nut11 = $N[0][10] /  $max_nutritions[10];
//echo $nut11.'</br>';
if($nut11 > 1) $nut11 = 1;
$nut12 = $N[0][11] /  $max_nutritions[11];
//echo $nut12.'</br>';
if($nut12 > 1) $nut12 = 1;
$nut13 = $N[0][12] /  $max_nutritions[12];
//echo $nut13.'</br>';
if($nut13 > 1) $nut13 = 1;
$nut14 = $N[0][13] /  $max_nutritions[13];
//echo $nut14.'</br>';
if($nut14 > 1) $nut14 = 1;
$nut15 = $N[0][14] /  $max_nutritions[14];
//echo $nut15.'</br>';
if($nut15 > 1) $nut15 = 1;
$nut16 = $N[0][15] /  $max_nutritions[15];
//echo $nut16.'</br>';
if($nut16 > 1) $nut16 = 1;
$nut17 = $N[0][16] /  $max_nutritions[16];
//echo $nut17.'</br>';
if($nut17 > 1) $nut17 = 1;
$nut18 = $N[0][17] /  $max_nutritions[17];
//echo $nut18.'</br>';
if($nut18 > 1) $nut18 = 1;
$nut19 = $N[0][18] /  $max_nutritions[18];
//echo $nut19.'</br>';
if($nut19 > 1) $nut19 = 1;
$nut20 = $N[0][19] /  $max_nutritions[19];
//echo $nut20.'</br>';
if($nut20 > 1) $nut20 = 1;
$nut21 = $N[0][20] /  $max_nutritions[20];
//echo $nut21.'</br>';
if($nut21 > 1) $nut21 = 1;
$nut22 = $N[0][21] /  $max_nutritions[21];
//echo $nut22.'</br>';
if($nut22 > 1) $nut22 = 1;
$nut23 = $N[0][22] /  $max_nutritions[22];
//echo $nut23.'</br>';
if($nut23 > 1) $nut23 = 1;
?>
<!DOCTYPE html>
<html>
<body>
  <canvas id="canvas" width="500" height="500"></canvas>
  <script src="jquery-3.3.1.min.js"></script>
  <script src="jcanvas.min.js"></script>
  <script>

 $(function ($) {
  //jQueryを使用しています
  //画面ロードイベントでcanvasの描画処理を実行します
  var canvas = $('#canvas')[0];
  if (canvas.getContext){ //古いブラウザなどcanvasが未対応の場合は処理を行わない
    var context = canvas.getContext('2d');

    //パスの初期化
    context.beginPath();
    //サブパスの起点を指定
    var centerX = 300;
    var centerY = 250;
    var rad = 200;
    context.moveTo(centerX + rad, centerY);
    //座標を指定してサブパスに追加
    context.lineTo(Math.cos(Math.PI * 2 * 1 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 1 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 2 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 2 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 3 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 3 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 4 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 4 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 5 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 5 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 6 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 6 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 7 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 7 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 8 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 8 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 9 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 9 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 10 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 10 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 11 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 11 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 12 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 12 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 13 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 13 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 14 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 14 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 15 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 15 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 16 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 16 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 17 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 17 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 18 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 18 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 19 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 19 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 20 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 20 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 21 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 21 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 22 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 22 / 23) * rad + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 23 / 23) * rad + centerX, Math.sin(Math.PI * 2 * 23 / 23) * rad + centerY);
    context.closePath();
    //定義したサブパスの領域を塗りつぶし
    context.fillStyle = 'rgb(255, 255, 255)';
    context.fill();
    context.strokeStyle = 'rgb(0, 0, 0)';
    context.stroke();

    context.beginPath();
    context.moveTo(centerX + rad, centerY);
    //座標を指定してサブパスに追加
    context.lineTo(Math.cos(Math.PI * 2 * 1 / 23) * rad * <?php echo $nut1; ?> + centerX, Math.sin(Math.PI * 2 * 1 / 23) * rad * <?php echo $nut1; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 2 / 23) * rad * <?php echo $nut2; ?>  + centerX, Math.sin(Math.PI * 2 * 2 / 23) * rad * <?php echo $nut2; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 3 / 23) * rad * <?php echo $nut3; ?>  + centerX, Math.sin(Math.PI * 2 * 3 / 23) * rad * <?php echo $nut3; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 4 / 23) * rad * <?php echo $nut4; ?>  + centerX, Math.sin(Math.PI * 2 * 4 / 23) * rad * <?php echo $nut4; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 5 / 23) * rad * <?php echo $nut5; ?>  + centerX, Math.sin(Math.PI * 2 * 5 / 23) * rad * <?php echo $nut5; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 6 / 23) * rad * <?php echo $nut6; ?>  + centerX, Math.sin(Math.PI * 2 * 6 / 23) * rad * <?php echo $nut6; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 7 / 23) * rad * <?php echo $nut7; ?>  + centerX, Math.sin(Math.PI * 2 * 7 / 23) * rad * <?php echo $nut7; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 8 / 23) * rad * <?php echo $nut8; ?>  + centerX, Math.sin(Math.PI * 2 * 8 / 23) * rad * <?php echo $nut8; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 9 / 23) * rad * <?php echo $nut9; ?>  + centerX, Math.sin(Math.PI * 2 * 9 / 23) * rad * <?php echo $nut9; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 10 / 23) * rad * <?php echo $nut10; ?>  + centerX, Math.sin(Math.PI * 2 * 10 / 23) * rad * <?php echo $nut10; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 11 / 23) * rad * <?php echo $nut11; ?>  + centerX, Math.sin(Math.PI * 2 * 11 / 23) * rad * <?php echo $nut11; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 12 / 23) * rad * <?php echo $nut12; ?>  + centerX, Math.sin(Math.PI * 2 * 12 / 23) * rad * <?php echo $nut12; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 13 / 23) * rad * <?php echo $nut13; ?>  + centerX, Math.sin(Math.PI * 2 * 13 / 23) * rad * <?php echo $nut13; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 14 / 23) * rad * <?php echo $nut14; ?>  + centerX, Math.sin(Math.PI * 2 * 14 / 23) * rad * <?php echo $nut14; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 15 / 23) * rad * <?php echo $nut15; ?>  + centerX, Math.sin(Math.PI * 2 * 15 / 23) * rad * <?php echo $nut15; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 16 / 23) * rad * <?php echo $nut16; ?>  + centerX, Math.sin(Math.PI * 2 * 16 / 23) * rad * <?php echo $nut16; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 17 / 23) * rad * <?php echo $nut17; ?>  + centerX, Math.sin(Math.PI * 2 * 17 / 23) * rad * <?php echo $nut17; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 18 / 23) * rad * <?php echo $nut18; ?>  + centerX, Math.sin(Math.PI * 2 * 18 / 23) * rad * <?php echo $nut18; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 19 / 23) * rad * <?php echo $nut19; ?>  + centerX, Math.sin(Math.PI * 2 * 19 / 23) * rad * <?php echo $nut19; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 20 / 23) * rad * <?php echo $nut20; ?>  + centerX, Math.sin(Math.PI * 2 * 20 / 23) * rad * <?php echo $nut20; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 21 / 23) * rad * <?php echo $nut21; ?>  + centerX, Math.sin(Math.PI * 2 * 21 / 23) * rad * <?php echo $nut21; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 22 / 23) * rad * <?php echo $nut22; ?> + centerX, Math.sin(Math.PI * 2 * 22 / 23) * rad * <?php echo $nut22; ?>  + centerY);
    context.lineTo(Math.cos(Math.PI * 2 * 23 / 23) * rad * <?php echo $nut23; ?>  + centerX, Math.sin(Math.PI * 2 * 23 / 23) * rad * <?php echo $nut23; ?>  + centerY);
    context.closePath();
    //定義したサブパスの領域を塗りつぶし
    context.fillStyle = 'rgb(0, 255, 0)';
    context.fill();
  }
});
  </script>
</body>
</html>
