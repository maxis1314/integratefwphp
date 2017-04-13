<?php 

include_once(ROOT_PATH . 'includes/lib.debug.php');

class Util_View{
	static function print_a($title,$data){
		echo "<h2 align=center>$title</h2>";
		print_a($data);
	}
	
	static function show_alert($smarty,$title,$content){
		if(is_array($content)){
			$content=var_export($content,true);
		}
		$smarty->assign('message',array('title'=>$title,'content'=>$content));
		$smarty->display('alert.dwt');
	}

	static function print_table($title,$data){
		$gen_keys = array_keys($data[0]);
		//sort($gen_keys,SORT_STRING);
		echo "<h2 align=center>$title</h2>";

		$col = 'grey';
		echo "<table border=1>";
		print '<tr>';
		foreach($gen_keys as $k1){
			echo "<th>",$k1," </th>";
		}
		echo "</tr>";
		foreach($data as $k){
			$col == 'grey' ? $col = 'lighblue' : $col = 'grey';
			print '<tr style="background-color:#'.$col.';">';
			foreach($gen_keys as $k1){
				echo "<td>",$k[$k1]," </td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
}


