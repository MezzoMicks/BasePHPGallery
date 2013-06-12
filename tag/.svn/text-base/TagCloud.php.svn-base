<?php

	include_once('./entity/Tag.php');


	new TagCloud(Tag::getAll());
	


	class TagCloud {
	
		var $maxFontSize = 20;
		var $minRating = 1;
		var $maxRating = 30;
	
		public function TagCloud($tags) {
			$this->maxRating = Tag::getMaxWeight();
			foreach($tags as $tag) {
				print("<span style=\"font-size:".$this->calculateSize($tag->getWeight())."pt;\">");
				print($tag->getName()." ");
				print("</span>");
			}
		}
	
		private function calculateSize($rating) {
			$result = 1;
			if ($rating > $this->minRating) {
				$result = $this->maxFontSize * ($rating - $this->minRating) / ($this->maxRating - $this->minRating);
			}
			return $result + 5;
		}
	
	}
?>