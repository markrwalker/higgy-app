<?php

class MyGrid extends Grid {
	function format_team($field) {
		if ($this->current_row[$field] == $this->current_row['winner']) {
			$this->setTDParam($field,'style/color','green');
			$this->setTDParam($field,'style/font-weight','bold');
		}
		if ($this->current_row[$field] != $this->current_row['winner']) {
			$this->setTDParam($field,'style/color','red');
			$this->setTDParam($field,'style/font-weight','bold');
		}
		$this->setTDParam($field,'style/white-space','nowrap');
	}

	function format_oppteam($field) {
		if ($this->current_row[$field] == $this->current_row['winner']) {
			$this->setTDParam($field,'style/color','red');
			$this->setTDParam($field,'style/font-weight','bold');
		}
		if ($this->current_row[$field] != $this->current_row['winner']) {
			$this->setTDParam($field,'style/color','green');
			$this->setTDParam($field,'style/font-weight','bold');
		}
		$this->setTDParam($field,'style/white-space','nowrap');
	}

	function format_playing($field) {
		if (!empty($this->current_row['playing'])) {
			$this->setTDParam($field,'style/text-decoration','line-through');
		}
		$this->setTDParam($field,'style/white-space','nowrap');
	}
}

