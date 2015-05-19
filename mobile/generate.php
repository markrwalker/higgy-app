<?php

for ($i=29; $i<57; $i++) {
	echo "insert into users (team_id, password, created) values (".$i.", ".rand(100000,999999).", now());"."<br />";
}