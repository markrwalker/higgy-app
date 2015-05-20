<?php

for ($i=1001; $i<1029; $i++) {
	echo "insert into users (team_id, password, created) values (".$i.", ".rand(100000,999999).", now());"."<br />";
}