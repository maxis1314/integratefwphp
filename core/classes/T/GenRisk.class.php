<?php



class T_GenRisk extends DB_Table{
	function __construct(){
		parent::__construct('gen_risk');
	}
	
	function getGenRisk($type){
		return $this->cacheGet(array("type"=>$type));
	}
	
	function getGenWeightRR($type){
		$gen_risk = $this->getGenRisk($type);
		foreach($gen_risk as $one){
			$genname = $one['gen_pos'];
			$gendata[$genname] = $gen_raw_data[$genname];
		
			$ra_type = explode(":",$one['opt']);
			$ra_rr = explode(":",$one['weight_rr']);
		
			for($i=0;$i<count($ra_type);$i++){
				if(empty($genweight[$genname])){
					$genweight[$genname] = array();
				}
				$genweight[$genname][$ra_type[$i]] = $ra_rr[$i];
			}
		}
		return $genweight;
	}
	
	function getGenWeightOR($type){
		$gen_risk = $this->getGenRisk($type);
		foreach($gen_risk as $one){
			$genname = $one['gen_pos'];
			$gendata[$genname] = $gen_raw_data[$genname];
	
			$ra_type = explode(":",$one['opt']);
			$ra_rr = explode(":",$one['weight_or']);
	
			for($i=0;$i<count($ra_type);$i++){
				if(empty($genweight[$genname])){
					$genweight[$genname] = array();
				}
				$genweight[$genname][$ra_type[$i]] = $ra_rr[$i];
			}
		}
		return $genweight;
	}
	
	function getAllRiskCached($type){
		$cache_name = "CONSTANT_ALL_GEN_RISK_".$type;
		$data = Util_Cache::get($cache_name,-1);
		return $data;
	}

	function getAllRiskSpecialCached($type){
		$cache_name = "SPECIAL_CONSTANT_ALL_GEN_RISK_".$type;
		$data = Util_Cache::get($cache_name,-1);
		return $data;
	}
	
	function generateAllRiskCache($type){
		$cache_name = "CONSTANT_ALL_GEN_RISK_".$type;
		$data=$this->getAllRisk($type);
		Util_Cache::set($cache_name, $data);
	}

	function generateAllRiskCacheSpecial($type){
		$cache_name = "SPECIAL_CONSTANT_ALL_GEN_RISK_".$type;
		$data=$this->getAllRisk($type);

		$gen_risk = $this->cacheGet(array("type"=>$type),"gen_pos asc");

		$genweight_rr = $this->getGenWeightRR($type);
		$gen_keys = array_keys($genweight_rr);





		$gen_count = count($gen_risk);
		$new_data = array();
		foreach($data as $k=>$v){
			$ra = explode("_",$k);
			$new_row = array();
			for($i=0;$i<$gen_count;$i++){
				$new_row[$gen_risk[$i]['gen_pos']]=$ra[$i];
			}

			$risk = 1;
			foreach ($gen_keys as $k) {
				$value = $new_row[$k];
				$pos_risk = $genweight_rr[$k][$value];
				$risk *= $pos_risk;
			}

			$new_row['result']=array($v,$risk);
			$new_data[]=$new_row;
		}

		Util_Cache::set($cache_name, $new_data);
		Util_Cache::set_raw($cache_name.".json", json_encode($new_data));
	}
	
	function genGroupIdentity($a){
		
	}
	
	
	function getPosRisk($type,$gen_data){
		$gen_risk = $this->cacheGet(array("type"=>$type),"gen_pos asc");
		$pos="";
		foreach($gen_risk as $one){
			$pos.=$gen_data[$one['gen_pos']]."_";
		}
		return $pos;
	}
	function getAllRisk($type){
		$gen_risk = $this->get(array("type"=>$type),"gen_pos asc");

		
		$ra_rr = array();
		$ra_pr = array();
		$ra_opt= array();
		$ra_len = array();
		foreach($gen_risk as $one){
			$rr = explode(":",$one['weight_rr']);
			$ra_rr[] = $rr;
			
			$pr = explode(":",$one['pct']);
			$ra_pr[] = $pr;
			
			$opt = explode(":",$one['opt']);
			$ra_opt[] = $opt;
			
			$ra_len[] = count($rr);
		}
		
		
		
		$total = 1;
		foreach($ra_len as $one){
			$total *= $one;
		}
		
		
		
		$ra_rr_total=array();
		$ra_pr_total=array();
		$ra_opt_total=array();
		$now = $total;
		for($i=0;$i<$total;$i++){
			echo "$i/$total\n";

			$rr_total = 1;
			$pr_total = 1;
			$opt_total = "";
			$j = $i;
			
			foreach($ra_len as $k=>$one){
				$num = $j%$one;
				$j = ($j - $num)/$one;
				 
				$rr_total*=$ra_rr[$k][$num];
				$pr_total*=$ra_pr[$k][$num];
				$opt_total.=$ra_opt[$k][$num]."_";
			}
			$rr_total=round($rr_total,6);
			$pr_total=round($pr_total,6);
			$ra_rr_total[]=$rr_total;
			$ra_pr_total[]=$pr_total;
			$ra_opt_total[]=$opt_total;
		}
		
		
		$gen_group_pct = array();
		foreach($ra_rr_total as $k=>$one){
			echo "$k/$total\n";
			 
			$pct = 0;
			foreach($ra_rr_total as $s=>$two){
				if($two<$one){
					$pct+=$ra_pr_total[$s];
				}elseif($two==$one){
					$pct+=$ra_pr_total[$s]/2;
				}
			}
			$gen_group_pct[$ra_opt_total[$k]]=$pct;
		}
		
		
		
		return $gen_group_pct;
	}
	
}