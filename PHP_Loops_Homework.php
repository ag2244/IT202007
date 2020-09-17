<?php

/* HOW I DID THIS:
1) Made for loop to generate an array of 10 integers, multiples of 3 from 3 to 30
2) Made a foreach loop that assigned each integer in the array to variable $num
3) Checked if $num was even by doing if (($num% 2) == 0), which checks if the remainder after $num/2 is 0.
4) printed if true!
*/

$arr = [];

//Array of multiples of 3, from 3*1 to 3*10
for($i = 0; $i < 10; $i+=1){
    $arr[$i] = ($i + 1)*3;
}

foreach($arr as $num){
	if (($num% 2) == 0) {
    	echo $num;
        echo "<br>\n";
    }
}

?>