<?php
	class Ranking{
		private $mainColumn;
		private $tiebreakerOrder = array();
		private $scoresArray;
		private $order = 'asc';

		public function setMainColumn($mainColumn){
			$this->mainColumn = $mainColumn;
		}
		
		public function getMainColumn(){
			return $this->mainColumn;
		}
		
		public function addTiebreakerOrder($tiebreakerOrder){
			$this->tiebreakerOrder[] = $tiebreakerOrder;
		}

		public function cleanTiebreakerOrder(){
			$this->tiebreakerOrder = array();
		}
		
		public function getTiebreakerOrder(){
			return $this->tiebreakerOrder;
		}
		
		public function setScoresArray($scoresArray){
			if (is_array($scoresArray)){
				$this->scoresArray = $scoresArray;
			}
		}
		
		public function getScoresArray(){
			return $this->scoresArray;
		}
		
		public function setOrder($order){
			$this->order = $order;
		}
		
		public function getOrder(){
			return $this->order;
		}		

		private function sortArray(&$a, &$b){
			$tiebreakerOrder = $this->getTiebreakerOrder();
			$current_tiebreaker = 0;
			$total_tiebreaker = count($tiebreakerOrder);
			$mainColumn = $this->getMainColumn();
			
			if (!isset($a[$mainColumn])){ // If mainColumn doesn't exists in array $a, define it to 0
				$a[$mainColumn] = 0;
			}

			if (!isset($b[$mainColumn])){ // If mainColumn doesn't exists in array $b, define it to 0
				$b[$mainColumn] = 0;
			}

			if ($a[$mainColumn] == $b[$mainColumn]) {
				while ($current_tiebreaker < $total_tiebreaker){
					$ref = $tiebreakerOrder[$current_tiebreaker];
					if (!isset($a[$ref])){
						$a[$ref] = 0;
					}

					if (!isset($b[$ref])){
						$b[$ref] = 0;
					}
					
					if ($a[$ref] != $b[$ref]) {
						return ($a[$ref] < $b[$ref]) ? 1 : -1;
					}
					
					$current_tiebreaker++;
				}
			}

			return ($a[$mainColumn] < $b[$mainColumn]) ? 1 : -1;
		}

		private function definePositions(){
			$scoresArray = $this->getScoresArray();
			$tiebreakerOrder = $this->getTiebreakerOrder();
			$total_tiebreaker = count($tiebreakerOrder);
			$mainColumn = $this->getMainColumn();

			if (is_array($scoresArray)){
				$position = 1;
				$prev = null;
				foreach ($scoresArray as &$current){
					$current_tiebreaker = 0;
					
					if (!isset($current[$mainColumn])){ // If mainColumn doesn't exists in array $current, define it to 0
						$current[$mainColumn] = 0;
					}

			
					if ($prev){
						if (!isset($prev[$mainColumn])){ // If mainColumn doesn't exists in array $prev, define it to 0
							$prev[$mainColumn] = 0;
						}
						
						if ($current[$mainColumn] != $prev[$mainColumn]){
							$position++;
						} else { // Regras de desempate
							while ($current_tiebreaker < $total_tiebreaker){
								$ref = $tiebreakerOrder[$current_tiebreaker];
								if (!isset($current[$ref])){
									$current[$ref] = 0;
								}

								if (!isset($prev[$ref])){
									$prev[$ref] = 0;
								}
								
								if ($current[$ref] != $prev[$ref]) {
									$position++;
									break;
								}
								$current_tiebreaker++;								
							}
						}
					}
					$current['position'] = $position;
					$prev = $current;
				}
			}
			return $scoresArray;
		}		
		
		public function createRanking(){
			uasort($this->scoresArray,array($this,'sortArray'));
			$order = $this->getOrder();
			if ($order == 'asc'){
				return array_reverse($scoresArray);
			}
			$scoresArray = $this->definePositions();
			
			return $scoresArray;
		}
	}
?>