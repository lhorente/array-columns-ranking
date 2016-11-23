<?php
	echo "<pre>";
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
			
	require '../lib/Ranking.php';
	$arquivo = 'scores1.csv';
	
	$linhas = 0;
	$row = 1;
	$header = array();
	$scores = array();
	
	if (($handle = fopen($arquivo, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
			if ($row == 1){
				$header = $data;
			} else {
				$score = array();
				foreach ($header as $h_idx=>$h){
					if (isset($data[$h_idx])){
						$score[$h] = $data[$h_idx];
					}
				}
				$scores[] = $score;
			}
			$row++;
			$linhas++;
		}
	}
	
	$Ranking = new Ranking();
	$Ranking->setMainColumn('total');
	$Ranking->setScoresArray($scores);
	$Ranking->setOrder('desc');
	$Ranking->addTiebreakerOrder('site_visit');
	$Ranking->addTiebreakerOrder('completing_quiz');
	$Ranking->addTiebreakerOrder('completing_quiz_full');
	$positions = $Ranking->createRanking();
	if ($positions){
		echo "<table border='1'>
				<tr>
					<td>Posição</td>
					<td>Nome</td>
					<td>Pontos</td>
					<td>completing_quiz</td>
					<td>completing_quiz_full</td>
					<td>site_visit</td>
				</tr>";
		foreach ($positions as $position){
			echo "<tr>
					<td>".$position['position']."°</td>
					<td>".$position['name']."</td>
					<td>".(isset($position['total']) ? $position['total'] : "")."</td>
					<td>".(isset($position['completing_quiz']) ? $position['completing_quiz'] : "")."</td>
					<td>".(isset($position['completing_quiz_full']) ? $position['completing_quiz_full'] : "")."</td>
					<td>".(isset($position['site_visit']) ? $position['site_visit'] : "") . "</td>
				</tr>";
		}
		echo "</table>";
	}	
	// var_dump($positions);
?>