<?


	/*****************************************************************************************
		랜덤 문자열 발생해서 반환하여 / 비 로그인 사용자 인증 관련 문자열 비교 함수
		
		random_string($length)   -> $length 발생할 문자열 갯수
		-- return으로 문자열 보내줌.
		비로그인 스킨의 스팸성 게시물 방지를 위한 문자열 입력 함수
	*****************************************************************************************/

	function getColums($table){
		$connect = $dbConn[1];
		$array = array();
		if($table==""){
			echo 'not table';
		}else{
			$query = "SHOW COLUMNS FROM .$table";
			$result = mysql_query($query);
			
			while ($row = mysql_fetch_array($result)){
				
				array_push($array, $row[Field]);
				
			}
		}
		
		return $array;
		
	}
	function findParents2($id ="", $check =""){
		$connect = $dbInfo[1];
		$output = "";
		$query = "SELECT * FROM inner_category WHERE indexcode = ".$id;
		
		$checkResult = mysql_query($query);
		$checkRow = mysql_fetch_array($checkResult);
		if($id!=""){
			
			$query = "SELECT * FROM inner_category";
			if($checkRow[menu_level]==3){
				
				$query.=" WHERE indexcode ='{$id}'";
				$result = mysql_query($query);
				
				while($row = mysql_fetch_array($result)){
					
					$output.=$row[indexcode].";|;";
				}
			}else{
				
				$query.=" WHERE menu_parent ='{$id}'";
				$result = mysql_query($query);
				
				while($row = mysql_fetch_array($result)){
					
					if($row[menu_level]==2){
						$query = "SELECT * FROM inner_category WHERE menu_parent = ".$row[indexcode];
						$result2 = mysql_query($query); 
						while($row2 = mysql_fetch_array($result2)){
							$output.=$row2[indexcode].";|;";
						}
					}else if($row[menu_level]==3){
						$output.=$row[indexcode].";|;";
					}
				}
			}

		}else{
				
				
		}
		
		return array_filter(explode(";|;",$output));
	
	}
	
	function findParents($id ="", $check=""){
		//check 2
		$connect = $dbInfo[1];
		$output = "";
		$query = "SELECT * FROM inner_category ";
		
		if($id!=""){
			$query.=" WHERE indexcode ='{$id}'";
			
			$result = mysql_query($query);
			while($row = mysql_fetch_array($result)){
				
				$output.=$row['menu_subject'];
				if($row[menu_parent]!="")$output.=";|;".findParents($row[menu_parent],2);
			}
			
		}else{
			
			
		}
		
		if($check==""){
			
			$output = array_reverse(explode(";|;",$output));
		}
		
		return $output;
		
	}


	function menu($id ="")
	{
		$connect = $dbInfo[1];
		$output = "";
		$query = " SELECT * FROM inner_category";
		
		if($id==""){
			
			$query.=" WHERE menu_parent ='' ORDER BY menu_seq ASC";
		}else{
			
			$query.=" WHERE menu_parent ={$id} ORDER BY menu_seq ASC";
			
		}

		/* $find_bottom = findParents2($id,2);
		echo $id;
		
		$where = " WHERE category_indexcode in(";
		for($i=0; $i<count($find_bottom); $i++){
			if(count($find_bottom)-1==$i)$where.=$find_bottom[$i].")";
			else $where.=$find_bottom[$i].",";
			
		} */
		$result = mysql_query($query);
	  	if($result !=""){
	  		
	  		$output.="<ul>\n";
		 	while( $row = mysql_fetch_array($result)){
		 		if($row[menu_level]==2){
		 			
		 			$query = "SELECT * FROM inner_category WHERE menu_parent = ".$row[indexcode];
		 			$resultC = mysql_query($query);
		 		
		 			$dbData = array();
		 			 while($row2 = mysql_fetch_array($resultC)){
		 				
		 			 	array_push($dbData,$row2);
		 			} 
		 			
		 			$where = " WHERE category_indexcode in(";
		 			
		 			for($i=0; $i<count($dbData); $i++){
		 				
		 				
		 				if($i==count($dbData)-1)$where.=$dbData[$i][indexcode].")";
		 				else $where.=$dbData[$i][indexcode].",";
		 				
		 			}
		 			
		 			$query = "SELECT *, count(*) as count FROM inner_lab_m ".$where;
		 			$result2 = mysql_fetch_array(mysql_query($query));
		 			
		 			
		 		}else{
		 			
		 		 	$query = "SELECT count(*) as count FROM inner_lab_m WHERE category_indexcode = ".$row[indexcode];
		 			$result2 = mysql_fetch_array(mysql_query($query));
		 		}
		 		
		 		
		  		$output .= "<li><div class='tNav_div'><a class='subject' href='#update' ondblclick='updateTree(this,1)' data-no='{$row[indexcode]}'>".$row["menu_subject"]."</a><span class='tree_count'></span><span class='lab_count'>(".$result2[count].")</span></div>";
		  		$output .= menu($row["indexcode"]);
		  		$output .= "</li>";
		  	}
		  	$output.="</ul>\n";
		  	
	  	}else{
	  		
	  		echo $query.'connet 오류';
	  	}
	  	
	  	

		return $output;
	}

	function menu_hide($id ="")
	{
		$connect = $dbInfo[1];
		$data = array();
		$output = "";
		$query = " SELECT * FROM inner_category";
	
		if($id==""){
			$query.=" WHERE menu_parent ='' ORDER BY menu_seq ASC";
		}else{
			$query.=" WHERE menu_parent ={$id} ORDER BY menu_seq ASC";
		}
		$result = mysql_query($query);
		$count = mysql_num_rows($result);
		
			
		if($count > 0){
			if($result !=""){
				$output.="<ul style='display:none'>\n";
				while( $row = mysql_fetch_array($result)){
					$query = "SELECT * FROM inner_category WHERE menu_parent = '{$row[indexcode]}'";
					$result_ = mysql_query($query);
					$count_ = mysql_num_rows($result_);
					
					$output .= "<li><div class='tNav_div'>";
					if($count_!=0)$output .= "<button class='togle_plus'></button>";
					$output	.="<a class='subject' href='#update' onclick='openerReady(this)' data-no='{$row[indexcode]}'>".$row["menu_subject"]."</a><span class='tree_count'></span></div>";
					$output .= menu_hide($row["indexcode"]);
					$output .= "</li>";
				}
				$output.="</ul>\n";
				 
			}else{
			  
				echo $query.'connet 오류';
			}
		}else{
			
		}
	
	
		return $output;
	}
	
	function randomString($length) {
	    $randCode = array('1','2','3','4','5','6','7','8','9','0','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	    
	    mt_srand((double)microtime()*1000000);
	    for($i=1;$i<=$length;$i++) {
	    	$Rstring .= $randCode[mt_rand(1, count($randCode))];
	    	
	    	
	    }
	    return $Rstring;
    }




	/* make a GetLinkUrl */
	function sgetLink($string){

		$url = "";
		$stringDiv = explode("|", $string);
		$j=0;


		for($i=0; $i<count($stringDiv); $i++){
			$name = $stringDiv[$i];
			$value = trim($_GET[$name]);

			if($value != null){
				if($j == 0)
					$url .= $name."=".urlencode($value);
				else
					$url .= "&amp;".$name."=".$value;

				$j++;
			}

		}

		return $url;
		
	}


	/* make a backUrl */
	function sbackIncUrl($string, $opt = 0){

		$url = "inner.php?";
		$stringDiv = explode("|", $string);
		$j=0;

		for($i=0; $i<count($stringDiv); $i++){
			$name = $stringDiv[$i];
			$value = trim($_GET[$name]);

			if($value != null){
				if($j == 0){
					$url .= $name."=".urlencode($value);
				}else{
					if($opt != 0)
						$url .= "&".$name."=".urlencode($value);	// php 파일에서 header 이동용으로 사용하기 위함 : 사용법은 콤마와 0 이 아닌 숫자(예:1)를 넣으면 됨
					else
						$url .= "&amp;".$name."=".urlencode($value);	// 일반 a href 링크에 사용되는 링크 생성용
				}

				$j++;
			}

		}

		return $url;
		
	}


	/* make a php backUrl */
	function sbackPhpUrl($string){

		$url = "../../../inner.php?";
		$stringDiv = explode("|", $string);
		$j=0;

		for($i=0; $i<count($stringDiv); $i++){
			$name = $stringDiv[$i];
			$value = trim($_GET[$name]);

			if($value != null){
				if($j == 0)
					$url .= $name."=".urlencode($value);
				else
					$url .= "&amp;".$name."=".urlencode($value);

				$j++;
			}

		}

		return $url;
		
	}


	/* function whereSql2($string){
		
		$i=0;
		
		foreach($string as $key => $val){
			if($i==0)$where = " where ";
			else $where .=" and ";
			$name_type = substr($val, 0, 1);
			
			if($name_type =="!"){
				$name = str_replace("!","",$val);
				$type = "number";
			}else if($name_type == "%"){
				$name = str_replace("%","",$val);
				$type = "like";
			}else{
				$name = $val;
				$type = "string";
			}
			
			
			if($type=="number"){
				$where.=$key."=".$name." ";
			}else if($type=="like"){
				
				$where.=$key." like '%".$name."%'";
				
			}else{
				
				$where.=$key."='".$name."'";
			}

			
			$i++;
		}
		
		return $where;
	} */
	
 	function whereSql2($string, $string2, $whereFlag){

		$stringDiv = explode("|",$string);
		$stringDiv2 = explode("|",$string2);
		
		$i=0;
		$num = 0 ;
		if($whereFlag){
			
			for($i=0; $i<count($stringDiv); $i++){
				
				$name_type = substr($stringDiv[$i], 0, 1);
				
				if($name_type =="!"){
					$name = str_replace("!","",$stringDiv[$i]);
					$type = "number";
				}else if($name_type == "%"){
					$name = str_replace("%","",$stringDiv[$i]);
					$type = "like";
				}else if($name_type == "#"){
					$name = str_replace("#","",$stringDiv[$i]);
					$type = "in";
				}else{
					$name = $stringDiv[$i];
					$type = "string";
				}
				
				$value = htmlspecialchars(trim($_GET[$name]), ENT_QUOTES);

				
				
				if($value!=null || $value != ""){
					if($num==0) $where=" where ";
					else $where .=" and ";
					
					if($type=="number")
					{
						
						$where.= $stringDiv2[$i]."=".$value." ";
					}
					else if($type=="like")
					{
						$where.=" ".$stringDiv2[$i]." like '%".$value."%' ";
					}
					else if($type=="in")
					{
						$in_arr =findParents2($value);
						$where.= " ".$stringDiv2[$i]." in(";
						
						for($j=0; $j<count($in_arr); $j++){
							
							if(count($in_arr)-1==$j)$where.=$in_arr[$j].")";
							else $where.= $in_arr[$j].", ";
						}
					}
					else
					{
						$where.= $stringDiv2[$i]."='".$value."' ";
					}
					$num++;
				}
				
			}
			
		
		}
	
	return $where;
	
 	}
	

	/* make a where sql */
	function whereSql($string, $whereflag){

		$stringDiv = explode("|", $string);
		$j=0;

		if($whereflag){
			$where = " where ";
			for($i=0; $i<count($stringDiv); $i++){
				$name_tmp = $stringDiv[$i];
				$name_type = substr($name_tmp, 0, 1);
				if($name_type == "!"){
					$type = "number";
					$name = str_replace("!", "", $name_tmp);
				}else if($name_type == "%"){
					$type = "like";
					$name = str_replace("%", "", $name_tmp);
				}else{
					$type= "string";
					$name = $name_tmp;
				}


				//$value = htmlentities(trim($_GET[$name]));				
				$value = htmlspecialchars(trim($_GET[$name]), ENT_QUOTES);				

				if($value != null){
					if($j == 0){
						if($type=="number"){
							$where .= $name."=".$value;
						}else if($type=="like"){
							$where .= $name." like '%".$value."%' ";
						}else{
							$where .= $name."='".$value."' ";
						}
					}else{
						if($type=="number"){
							$where .= " and ".$name."=".$value;
						}else if($type=="like"){
							$where .= " and ".$name." like '%".$value."%' ";
						}else{
							$where .= " and ".$name."='".$value."' ";
						}
					}
					$j++;
				}
			}

		}else{
			$where = "";
			for($i=0; $i<count($stringDiv); $i++){
				$name = $stringDiv[$i];
				//$value = htmlentities(trim($_GET[$name]));				
				$value = htmlspecialchars(trim($_GET[$name]), ENT_QUOTES);

				if($value != null){
					$where .= " and ".$name."='".$value."' ";
					$j++;
				}
			}
		}

		if($j == 0)
			$where = "";

		return $where;
		
	}




	/* make a insert sql */
	function insertSql($string, $postValue, $table){

		$j=0;
		$stringDiv = explode("|", $string);

			$insert = "insert into ".$table;
			$field = " (";
			$fieldValue = " (";


			for($i=0; $i<count($stringDiv); $i++){
				$name_tmp = $stringDiv[$i];
				$name_type = substr($name_tmp, 0, 1);
				if($name_type == "!"){	// 숫자느낌 !!!
					$type = "number";
					$name = str_replace("!", "", $name_tmp);

				}else if($name_type == "*"){	 // 패스워드는 **** 표시
					$type = "password";
					$name = str_replace("*", "", $name_tmp);

				}else if($name_type == "#"){	 // base64로 암호화
					$type = "base64";
					$name = str_replace("#", "", $name_tmp);
				}else{
					$type= "string";
					$name = $name_tmp;
				}


				//$value = htmlentities(trim($_GET[$name]));				
				$value = htmlspecialchars(trim($postValue[$name]), ENT_QUOTES);		


				if($value != null){

					if($j == 0){
						$field .= $name;
						if($type=="number"){
							$fieldValue .= $value;

						}else if($type=="password"){
							$fieldValue .= "password('".$value."')";

						}else if($type=="base64"){
							$fieldValue .= base64_encode($value);

						}else{
							$fieldValue .= "'".$value."'";
						}

					}else{
						$field .= ",".$name;
						if($type=="number"){
							$fieldValue .= ",".$value;

						}else if($type=="password"){
							$fieldValue .= ",password('".$value."')";
						}else if($type=="base64"){
							$fieldValue .= base64_encode($value);

						}else{
							$fieldValue .= ",'".$value."'";
						}

					}
					$j++;

				}

			}

		$fieldValue .= ")";
		$field .= ")";


		if($j == 0)
			$insert = "";
		else
			$insert .= $field." values".$fieldValue;

		return $insert;
		
	}



	/* make a insert sql by Array */
	function insertSqlByArray($string, $arrayValue, $table){
		$stringDiv = explode("|", $string);
		$insert="";

		for($i=0; $i<count($arrayValue); $i++){

			for($j=0; $j<count($stringDiv); $j++){
				$name = $stringDiv[$j];
				$value = $arrayValue[$i][$name];
				$_POST[$name] = $value;
			}
			$insert .= insertSql($string, $_POST, $table).";\n";

		}
		return $insert;
		
	}


	/* make a update sql */
	function updateSql($string, $postValue, $table, $where = "indexcode"){

		$j=0;
		$stringDiv = explode("|", $string);

			$update = "update ".$table. " set ";
			$field = "";
			$fieldValue = "";


			for($i=0; $i<count($stringDiv); $i++){
				$name_tmp = $stringDiv[$i];
				$name_type = substr($name_tmp, 0, 1);
				$name_type2 = substr($name_tmp, 1, 1);

				if($name_type == "!"){
					$type = "number";
					$name = str_replace("!", "", $name_tmp);
				}else if($name_type == "*"){
					$type = "password";
					$name = str_replace("*", "", $name_tmp);
				}else if($name_type == "#"){
					$type = "blank";	 // 빈 공백값으로 저장할 경우에 - 문자형일 경우에만 해당
					$name = str_replace("#", "", $name_tmp);
				}else{
					$type= "string";
					$name = $name_tmp;
				}


				//$value = htmlentities(trim($_GET[$name]));				
				$value = htmlspecialchars(trim($postValue[$name]), ENT_QUOTES);		
	

				if(($value != null || $type == "blank") && $name != "no"){

					$field = $name;
					if($type=="number"){
						$fieldValue = $value;
					}else if($type=="password"){
						$fieldValue = "password('".$value."')";
					}else if($type == "blank"){
						$fieldValue = "'".$value."'";
					}else{
						$fieldValue = "'".$value."'";
					}

					if($j == 0)
						$update.=$field."=".$fieldValue;
					else
						$update.=",".$field."=".$fieldValue;


					$j++;

				}else{

				}

			}


		if($j == 0)
			$update = "";
		else
			$update .= " where {$where}=".$postValue[no];

		return $update;
		
	}





	/* make a update comment sql */
	function updateCommentSql($string, $postValue, $table){

		$j=0;
		$stringDiv = explode("|", $string);

			$update = "update ".$table. " set ";
			$field = "";
			$fieldValue = "";


			for($i=0; $i<count($stringDiv); $i++){
				$name_tmp = $stringDiv[$i];
				$name_type = substr($name_tmp, 0, 1);
				$name_type2 = substr($name_tmp, 1, 1);

				if($name_type == "!"){
					$type = "number";
					$name = str_replace("!", "", $name_tmp);
				}else if($name_type == "*"){
					$type = "password";
					$name = str_replace("*", "", $name_tmp);
				}else if($name_type == "#"){
					$type = "blank";	 // 빈 공백값으로 저장할 경우에 - 문자형일 경우에만 해당
					$name = str_replace("#", "", $name_tmp);
				}else{
					$type= "string";
					$name = $name_tmp;
				}


				//$value = htmlentities(trim($_GET[$name]));				
				$value = htmlspecialchars(trim($postValue[$name]), ENT_QUOTES);		

				if(($value != null || $type == "blank") && $name != "no"){

					$field = $name;
					if($type=="number"){
						$fieldValue = $value;
					}else if($type=="password"){
						$fieldValue = "password('".$value."')";
					}else if($type == "blank"){
						$fieldValue = "'".$value."'";
					}else{
						$fieldValue = "'".$value."'";
					}

					if($j == 0)
						$update.=$field."=".$fieldValue;
					else
						$update.=",".$field."=".$fieldValue;


					$j++;

				}else{

				}

			}


		if($j == 0)
			$update = "";
		else
			$update .= " where docindexcode=".$postValue[no]." and indexcode=".$postValue[cno];

		return $update;
		
	}





	/* make a update sql by Array */
	function updateSqlByArray($string, $arrayValue, $table){
		$stringDiv = explode("|", $string);
		$insert="";

		for($i=0; $i<count($arrayValue); $i++){

			for($j=0; $j<count($stringDiv); $j++){
				$name = $stringDiv[$j];
				$value = $arrayValue[$i][$name];
				$_POST[$name] = $value;
			}
			$insert .= updateSql($string, $_POST, $table).";\n";

		}
		return $insert;
		
	}




	/* make byes to formated file size text */
	function sfileSizeTxt($size) {	 // size unit is bytes
		//$fileInfo = array();
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;

		//$fileInfo[rank] =  $i;
		//$fileInfo[size] =  round($size, 2);
		//$fileInfo[unit] = $units[$i];
		//$fileInfo[txt] = round($size, 2)." ".$units[$i];

		return  round($size, 2).$units[$i];
	}



	/* make file size text to byes value */
	function sfileSizeTxtToByte($value, $unitText = "") {
		$fileInfo = array();
		if($unitText == ""){
			$unit_tmp = trim($value);
			$unit = substr($unit_tmp, -1);
			$unitValue = intval(substr($unit_tmp, 0, -1));

		}else{
			$unit = trim($unitText);
			$unitValue = 0;
		}
		$resultValue = 0;

		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		$units2 = array('B', 'K', 'M', 'G', 'T');
		if(in_array($unit, $units) || in_array($unit, $units2)){
			for($i=0; $i<count($units); $i++){
				if($units[$i] == $unit || $units2[$i] == $unit){
					$unitValue = pow(1024, $i);
					break;
				}
			}
			$resultValue = intval($value) * $unitValue;
		}

		return $resultValue;
	}




	/* get the last msg of array */
	function srecent($_array){
		$msg_count = count($_array);
		if($msg_count > 0)
			$msg = $_array[$msg_count-1];

		return $msg;
	}


	/* return the value of array with new msg into array */
	function sadd($_status, $msg){
		$msg_count = count($_status);
		$_status[$msg_count] = $msg;
		return $_status;
	}



	/* make a string using array by system charater 1 and 2 */
	function sarrayToString($_system, $sdata){		
		$txtData = "<?";
		$char1 = $_system[data][char][char1];
		$char2 = $_system[data][char][char2];

		foreach ($sdata as $key => $value){
			if(trim($key) != "backUrl")
				$txtData .= trim($key).$char1.trim($value).$char2;

		}		
		return $txtData."?>";

	}


	/* make a array using string by system charater 1 and 2 */
	function sstringToArray($_system, $string){
		$string = str_replace("<?", "", $string);
		$string = str_replace("?>", "", $string);

		$arrData = array();
		$char1 = $_system[data][char][char1];
		$char2 = $_system[data][char][char2];

		$stringDiv = explode($char2, $string);

		for($i=0; $i<count($stringDiv); $i++){
			$tmpDiv = explode($char1, $stringDiv[$i]);
			$arrName = trim($tmpDiv[0]);

			if($arrName != null){
				$arrValue = trim($tmpDiv[1]);
				$arrData[$arrName] = $arrValue;

			}
		}
		return $arrData;

	}



	/* return the infomation by Array from file */
	function sread($_system, $sfile){
		if(file_exists($sfile)){
			$sdata = "";
			$fp = fopen($sfile,"r"); 
			while (!feof($fp)) {
			  $sdata .= fread($fp,1024);
			}
			fclose($fp);

		}else{
			echo "can't find file : ".$sfile;
		}
		return sstringToArray($_system, $sdata);

	}


	/* return the infomation by just text from file */
	// this function is created for inc file read in admin mode.
	function sreadText($_system, $sfile){
		if(file_exists($sfile)){
			$sdata = "";
			$fp = fopen($sfile,"r"); 
			while (!feof($fp)) {
			  $sdata .= fread($fp,1024);
			}
			fclose($fp);

		}else{
			echo "can't find file : ".$sfile;
		}
		return $sdata;

	}


	/* set the Array infomation to file */
	function swrite($_system, $sfile, $sdata){

			if(!file_exists($sfile))
				$needChmod = true;	 // 처음 생성되는 파일일 경우에 나중에 ftp 로 수정할때 저장이 안되므로 권한 변경이 필요함

			if($needChmod)
				@chmod($sfile, 0777);	// 처음 생성된 파일이 만들어지고 난후에는 권한을 쓰기 권한으로 설정해줌

			$fp = fopen($sfile,"w"); 
			if(fwrite($fp, $sdata) == false){
				echo "can't write to file : ".$sfile;
			}
			fclose($fp);


	}

	/* insert text to each value of array and return the new array */
	function sinsertText($text, $arr){
		for($i=0; $i<count($arr); $i++){
			$arr[$i] = $text.$arr[$i];
		}
		return $arr;
	}
	



	/* return array value by using text with div string */
	function sanalysis($_system, $dataText){

		$dataOut = array();

		$dataDiv = explode($_system[data][char][char3][1], $dataText);

		for($i=0; $i<count($dataDiv); $i++){
			$dataOutDiv = explode($_system[data][char][char3][2], $dataDiv[$i]);
			$dataOut[$dataOutDiv[0]] = $dataOutDiv[1];
		}
		return $dataOut;
	}



	/* return the file array from path */
	function sgetPathInfo($path){
		$fileArray = null;
		$dirArray = null;

		$i=0; $j=0;

		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if (is_dir($path."/".$file) != true) {
					$fileArray[$i++] =$file;
				}else{
					if($file != "." && $file != ".."){
						$dirArray[$j++] =$file;
					}
				}
			}
			closedir($handle);
		}

		$pathInfo[fileInfo] = $fileArray;
		$pathInfo[dirInfo] = $dirArray;


		return $pathInfo;
	}


	/*****************************************************************************************
		파일이름에서 확장자만 리턴함
	*****************************************************************************************/
	function getFileExtention($filename){
		$ext = "";
		$tmpDiv = explode(".", $filename);
		$ext = $tmpDiv[count($tmpDiv)-1];
		return strtolower($ext);
	}




	/*****************************************************************************************
		DB connect
	*****************************************************************************************/
	function dbConnect($dbConn){

		$db_server = $dbConn[dbserver];		
		$db_id = $dbConn[id];
		$db_pass = $dbConn[pw];
		$dbName = $dbConn[dbname];


		$connect = mysql_connect($db_server, $db_id, $db_pass);

		if (!$connect) {
			//die("Could not connect to db server. DB server name is ".$db_server.".<br>");
			$msg = "Could not connect to db server. DB server name is ".$db_server;
			$dbInfo[0] = false;

		}else{
			$dbInfo[0] = true;
			mysql_select_db($dbName,$connect);
			mysql_query("SET NAMES 'utf8'");	// 디비에 한글 저장하거나 읽어올때 깨지는 문제 해결을 위한 방법
			$msg = "DB Connected to ".$db_server;
		}

		$dbInfo[1] = $connect;
		$dbInfo[2] = $dbName;
		$dbInfo[3] = $msg;

		return $dbInfo;

	}


	/*****************************************************************************************
		Get Data from DB connection by query
	*****************************************************************************************/

	function getDBData($dbConn, $query){
		$connect = $dbConn[1];
		$data = array();
		$i=0;

		$result = mysql_query($query, $connect); 

		if($result != null){
			
			while($row=mysql_fetch_array($result)){

				// etc 변수에 저장된 값을 etc 배열에 저장하도록 함
				if(trim($row[etc]) != ""){
					$etcDiv = explode(";|;", $row[etc]);
					for($j=0; $j<count($etcDiv); $j++){
						$keyDiv = explode(";=;", $etcDiv[$j]);
						if($keyDiv[0] != ""){
							$etc[trim($keyDiv[0])] = trim($keyDiv[1]);
						}

					}
					$row[etcs] = $etc;
				}


				$data[$i] = $row;
				$i++;
			}
			

		}else{
			$dbName = $dbConn[2];
			echo "<p>Can not find db table( ".$dbName. " ) or wrong query ( ".$query." )</p>";
		}

		return $data;

	}

	function getSDBData($dbConn, $query){
		$connect = $dbConn[1];
		$data = array();
		$i=0;
	
		$result = mysql_query($query, $connect);
	
		if($result != null){
				
			while($row=mysql_fetch_array($result)){
	
				// etc 변수에 저장된 값을 etc 배열에 저장하도록 함
				if(trim($row[etc]) != ""){
					$etcDiv = explode(";|;", $row[etc]);
					for($j=0; $j<count($etcDiv); $j++){
						$keyDiv = explode(";=;", $etcDiv[$j]);
						if($keyDiv[0] != ""){
							$etc[trim($keyDiv[0])] = trim($keyDiv[1]);
						}
	
					}
					$row[etcs] = $etc;
				}
	
	
				$data[$i] = $row;
				$i++;
			}
				
	
		}else{
			$dbName = $dbConn[2];
		}
	
		return $data;
	
	}

	/*****************************************************************************************
		run query by DB connection
	*****************************************************************************************/

	function runQuery($dbConn, $query){
		$connect = $dbConn[1];
		$data = array();

		$result = mysql_query($query, $connect); 

		if($result != null){

		}else{
			$dbName = $dbConn[2];
			echo "<p>Can not find db table( ".$dbName. " ) or wrong query ( ".$query." )</p>";
		}

		return $data;

	}
	
	function runQueryT($dbConn, $query){
		$connect = $dbConn[1];
		$data = array();
	
		$result = mysql_query($query, $connect);
	
		if($result != null){
	
		}else{
			$dbName = $dbConn[2];
			
		}
	
		return $data;
	
	}
	



	function dbClose($dbConn){
		$connect = $dbConn[1];
		mysql_close($connect);
	}



	/* if the value is same, return checked text */
	function ischecked($value1, $value2){
		$value = null;
		
			if(trim($value1) == trim($value2))
				$value = "checked=\"checked\"";

			return $value;

	}


	/* if the value is same, return checked text */
	function isselected($value1, $value2){
		$value = null;
		
			if(trim($value1) == trim($value2))
				$value = "selected=\"selected\"";

			return $value;

	}



	/*****************************************************************************************
		데이터에서 파일 정보 반환
	*****************************************************************************************/
	function data2fileArray($_charArray, $data){
		$returnValue = array();
		$dataDiv = explode($_charArray[0], $data);
		$j=0;
		for($i=0; $i<count($dataDiv); $i++){
			if($dataDiv[$i] != ""){
				$infoDiv = explode($_charArray[1], $dataDiv[$i]);
				$infoDiv[ext] = getFileExtention($infoDiv[0]);
				$returnValue[$j++] = $infoDiv;
			}

		}
		return $returnValue;
	}



	/*****************************************************************************************
		php 파일 업로드
	*****************************************************************************************/
	function checkFile($file_tempPathAndName, $file_realName, $datafilePath){

		$returnMsg = true;		

		if(is_uploaded_file($file_tempPathAndName)){			
			$pathAndName = $datafilePath . "/" . $file_realName;		

			if(file_exists($pathAndName)){
				$returnMsg = "same : 동일한 이름을 가진 파일이 존재합니다.";
			}else{
				//$file_content = strip_tags(file_get_contents($file_tempPathAndName));
				//file_put_contents($file_tempPathAndName, $file_content);

			}

			if(!move_uploaded_file($file_tempPathAndName, $pathAndName)){
				$returnMsg = "fail : 파일 업로드에 실패했습니다.";
			}
		}else{
			$returnMsg = false;
		}

			return $returnMsg;
	}



	/*****************************************************************************************
		php 파일 업로드 후 압축 풀기
	*****************************************************************************************/
	function checkFileZIP($file_tempPathAndName, $file_realName, $datafilePath){

		$returnMsg = true;

		if(is_uploaded_file($file_tempPathAndName)){			
			$pathAndName = $datafilePath . "/" . $file_realName;
			$zIp_path = str_replace(".zip", "", $pathAndName);

			if(file_exists($pathAndName)){
				$returnMsg = "same : 동일한 이름을 가진 파일이 존재합니다.";
			}

			if(!move_uploaded_file($file_tempPathAndName, $pathAndName)){
				$returnMsg = "fail : 파일 업로드에 실패했습니다.";
			}

			// zip 파일 압축풀기
			include "pclzip.php";

			$_PCLZIP = new PclZip($pathAndName);

			@mkdir($zIp_path, 0766);
			$_PCLZIP->extract(PCLZIP_OPT_PATH, $zIp_path, PCLZIP_OPT_REMOVE_PATH, '');
			@chmod($zIp_path, 0766);



		}else{
			$returnMsg = false;
		}

			return $returnMsg;
	}



	/*****************************************************************************************
		php 파일 업로드
	*****************************************************************************************/

	function getNewFileName($file_tempName){
		$tmp="";
		$filename = $file_tempName;
		$tmp.=date("YmdHis", time());
		$tmp.="_".$filename;
		return $tmp;
	}


	/*****************************************************************************************
		blank check
	*****************************************************************************************/
	function blankCheck($_arrValue){
		$blankResult = array();
		$j=0;
		for($i=0; $i<count($_arrValue); $i++){
			if(trim($_arrValue[$i]) == ""){
				$j++;
				$blankResult[isblank] = true;
				$blankResult[num] = $i;
				break;
			}

		}

		if($j==0){
			$blankResult[isblank] = false;
		}

		return $blankResult;

	}



	/*****************************************************************************************
		grant check
	*****************************************************************************************/
	function grantCheck($_system, $mode, $_level, $user_level, $data){


		$_sess = $_system[session][info];
		$check[access] = false;
		$check[msg] = "";


		//echo "<br>mode : ".$mode;
		//echo "<br>_level : \n".print_r($_level);
		//echo "<br>user_level : ".intval($user_level);
		//echo "<br>data : \n".print_r($data);

		if($_mode != "list"){	// 리스트에서는 권한 체크만 하면 되고 그 외에 모드에서는 데이터간의 중요간 차이가 있기 때문에 따로 처리함

				$db_upw = $_system[html][dbdata][0][upw];
				$db_userid = $_system[html][dbdata][0][uid];

				/* db login mode */
				if($db_userid != "")
					$db_loginMode = true;			// 로그인 사용자의 글
				else
					$db_loginMode = false;		// 비로그인 사용자의 글


				/* session login mode */
				if($_sess[user_uid]!= "")
					$sess_loginMode = true;		// 로그인 상태
				else
					$sess_loginMode = false;	// 비로그인 상태


				$db_secret = intval($_system[html][dbdata][0][issecret]);
				/* secret mode */
				if($db_secret != 0)
					$secret_mode = true;		// 비밀글
				else
					$secret_mode = false;		// 공개를



				if($_sess[user_uid] == $db_userid && $sess_loginMode==true )
					$writer_mode = true;			// 자신의 글
				else
					$writer_mode = false;		// 타인의 글 또는 비로그인 사용자의 글


				/* mode type */
				if($db_loginMode){				// 위의 값을 가지고 모드 타입값을 정함 (이것으로 예외 처리할때 쉽게 하기 위함)
					if($sess_loginMode)
						$mode_type = 1;			// 로그인 사용자글이며 현재 로그인한 상태
					else
						$mode_type = 2;			// 로그인 사용자글이며 현재 비로그인 상태

				}else{
					if($sess_loginMode)
						$mode_type = 3;			// 비로그인 사용자글이며 현재 로그인한 상태
					else
						$mode_type = 4;			// 비로그인 사용자글이며 현재 비로그인 상태

				}


		}


		$_backUrl = sbackPhpUrl("sMenu|mode|no|pno|category|limit|".$_GET[sfv]."|sfv|opt");
		if($user_level <= intval($_level[auth_admin])){	 // 관리자 기능 되는지 유무를 체크 - 리스트와 뷰 등에서 사용
			$check[auth_admin] = true;
		}


		switch($mode)	{

			case ("admin") :
				$_cancleUrl = sbackIncUrl("sMenu|pno|category|limit|".$_GET[sfv]."|sfv|opt")."&mode=list";

				if($user_level <= intval($_level[auth_admin])){
					$check[access] = true;
				}else{
					$check[access] = false;
						$_msg = "page;=;noauth;|;msg;=;권한이 없습니다.[a1];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
						$check[msg] = $_msg;

				}

				return $check;






			case ("list") :
				$_cancleUrl = "";

				if($user_level <= intval($_level[auth_list])){
					$check[access] = true;

				}else{
					if($sess_loginMode){
						$_msg = "page;=;noauth;|;msg;=;권한이 없습니다.[l1];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
						$check[msg] = $_msg;
						$check[access] = false;
					}else{
						$_msg = "page;=;login;|;msg;=;로그인이 필요합니다.[l2];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
						$check[msg] = $_msg;
						$check[access] = false;
					}

				}	
				
				// 리스트 페이지에서 글작성 권한을 미리 가져옴
				if($user_level <= intval($_level[auth_write])){
					$check[auth_write] = true;
				}

				return $check;





			case ("view") :
				$_cancleUrl = sbackIncUrl("sMenu|pno|category|limit|".$_GET[sfv]."|sfv|opt")."&mode=list";

			//print_r($_level);
			//print_r($_system[html]);
			//echo $writer_mode;

				if($secret_mode == true){
					if($user_level <= intval($_level[auth_secret]) || $writer_mode == true ){		// 비밀글 읽을 수 있는 권한자와 그 비밀글 작성자는 글 읽을 수 있도록 함
						$check[access] = true;						

					}else{

						if($mode_type == 1){
							$_msg = "page;=;noauth;|;msg;=;권한이 없습니다.[v1];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
							$check[msg] = $_msg;
							$check[access] = false;

						}else if($mode_type == 2){
							//print_r($data);
							if($data[upw] != ""){
								if($_GET[upw] !=""){
									$upw_tmp = base64_decode($_GET[upw]);
									$upw_real = substr($upw_tmp, 8, strlen($upw_tmp)-8);
									if($db_upw == $upw_real){
										$check[access] = true;

									}else{
										$_msg = "page;=;upw;|;msg;=;암호가 틀렸습니다. 암호를 다시 입력해주세요.[v4];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
										$check[msg] = $_msg;
										$check[access] = false;

									}

								}else{
										$_msg = "page;=;upw;|;msg;=;암호를 입력해주세요.[v5];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
										$check[msg] = $_msg;
										$check[access] = false;
								}


							}else{
								$_msg = "page;=;login;|;msg;=;로그인이 필요합니다.[v2];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
								$check[msg] = $_msg;
								$check[access] = false;
							}



						}else if($mode_type == 3){
							$_msg = "page;=;noauth;|;msg;=;권한이 없습니다.[v3];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
							$check[msg] = $_msg;
							$check[access] = false;


						}else{
							if($_GET[upw] !=""){
								$upw_tmp = base64_decode($_GET[upw]);
								$upw_real = substr($upw_tmp, 8, strlen($upw_tmp)-8);
								if($db_upw == $upw_real){
									$check[access] = true;

								}else{
									$_msg = "page;=;upw;|;msg;=;암호가 틀렸습니다. 암호를 다시 입력해주세요.[v4];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
									$check[msg] = $_msg;
									$check[access] = false;

								}

							}else{
									$_msg = "page;=;upw;|;msg;=;암호를 입력해주세요.[v5];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
									$check[msg] = $_msg;
									$check[access] = false;
							}

						}
						
					}


						/* 답글을 달면 원문의 아이디는 rid 에 저장이 된다. 이는 원문의 사용자의 답글이 비밀글이라도 원문의 사용자는 볼 수 있도록 하기 위함이다. */
						$db_rid = $_system[html][dbdata][0][rid];
						if($_sess[user_uid] == $db_rid && $sess_loginMode==true )
							$check[access] = true;



				}else{
					if($user_level <= intval($_level[auth_view])){
						$check[access] = true;

					}else{
						if($sess_loginMode){
							$_msg = "page;=;noauth;|;msg;=;권한이 없습니다.[v6];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
							$check[msg] = $_msg;
							$check[access] = false;
						}else{
							$_msg = "page;=;login;|;msg;=;로그인이 필요합니다.[v7];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
							$check[msg] = $_msg;
							$check[access] = false;
						}

					}

				}


				// 뷰페이지에서 글작성 권한을 미리 가져옴
				if($user_level <= intval($_level[auth_write])){
					$check[auth_write] = true;
				}

				// 뷰페이지에서 답글 쓸 권한을 미리 가져옴
				if($user_level <= intval($_level[auth_reply])){
					$check[auth_reply] = true;
				}

				// 뷰페이지에서 filedown 권한을 미리 가져옴
				if($user_level <= intval($_level[auth_filedown])){
					$check[auth_filedown] = true;
				}

				// 뷰페이지에서 alltag 권한을 미리 가져옴
				if($user_level <= intval($_level[auth_alltag])){
					$check[auth_alltag] = true;
				}

				// 뷰페이지에서 delete 권한을 미리 가져옴
				if($user_level <= intval($_level[auth_delete])){
					$check[auth_delete] = true;
				}

				// 뷰페이지에서 comment 권한을 미리 가져옴
				if($user_level <= intval($_level[auth_comment])){
					$check[auth_comment] = true;
				}



				return $check;






			case ("reply") :
					$_cancleUrl = sbackIncUrl("sMenu|no|pno|category|limit|".$_GET[sfv]."|sfv|opt")."&mode=view";

					if($user_level <= intval($_level[auth_reply])){
						$check[access] = true;

					}else{

						if($sess_loginMode){
							$_msg = "page;=;noauth;|;msg;=;권한이 없습니다.[r1];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
							$check[msg] = $_msg;
							$check[access] = false;
						}else{
							$_msg = "page;=;login;|;msg;=;로그인이 필요합니다.[r2];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
							$check[msg] = $_msg;
							$check[access] = false;
						}


					}

					// 공지를 쓸 권한이 있는지 미리 가져옴
					if($user_level <= intval($_level[auth_gongji])){
						$check[auth_gongji] = true;
					}

					// 파일 업로드 권한이 있는지 미리 가져옴
					if($user_level <= intval($_level[auth_fileup])){
						$check[auth_fileup] = true;
					}



					return $check;






			case ("write") :
					$_cancleUrl = sbackIncUrl("sMenu|pno|category|limit|".$_GET[sfv]."|sfv|opt")."&mode=list";

					if($user_level <= intval($_level[auth_write])){
						$check[access] = true;

					}else{

						if($sess_loginMode){
							$_msg = "page;=;noauth;|;msg;=;권한이 없습니다.[w1];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
							$check[msg] = $_msg;
							$check[access] = false;
						}else{
							$_msg = "page;=;login;|;msg;=;로그인이 필요합니다.[w2];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
							$check[msg] = $_msg;
							$check[access] = false;
						}

					}

					// 공지를 쓸 권한이 있는지 미리 가져옴
					if($user_level <= intval($_level[auth_gongji])){
						$check[auth_gongji] = true;
					}

					// hmode 값은 글 쓸때에 sql.php 에서 레벨값에 의해 정해짐. 그리고 뷰페이지에서는 그 값으로 허용 html을 변환하여 보여주므로 여기서는 태그관련 처리 불필요함
	

					// 글작성 페이지에서 fileup 권한을 미리 가져옴
					if($user_level <= intval($_level[auth_fileup])){
						$check[auth_fileup] = true;
					}


					return $check;





			case ("update") :
			case ("delete") :
					$_cancleUrl = sbackIncUrl("sMenu|no|pno|category|limit|".$_GET[sfv]."|sfv|opt")."&mode=view";

					if($user_level <= intval($_level[auth_write])){	//  수정 권한은 관리자 등급과 일반으로 나누고.. 관리자인 경우에는 해당 아이디가 관리자라면 그보다 레벨이 작은 관리자는 수정 못하게 하자. 그러나 본인의 글인 경우에는 수정가능하도록 하자. 						

							if($user_level <200){		// 관리자 등급의 등록 수정,삭제 권한

									if($writer_mode){			// 관리자 자신의 글
										$check[access] = true;

									}else{
										if($db_loginMode){	// 로그인 사용자의 글

											if($user_level <=100){	// 최고 관리자는 모두 수정 가능
												$check[access] = true;

											}else{							// 그 외의 관리자들은 회원의 레벨값에 따라 수정가능

												$dbInfo = dbConnect($_system[dbconn]);
												$query = "select authlevel from userinfo where userid='".$data[uid]."'";
												$dbData = getDBData($dbInfo, $query);
												$db_userlevel = intval($dbData[0][authlevel]);
												dbClose($dbInfo);

												if($db_userlevel < 200){
													$_msg = "page;=;noauth;|;msg;=;권한이 없습니다.[u1];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
													$check[msg] = $_msg;
													$check[access] = false;		// 관리자 그룹의 글 수정 불가능

												}else{

													$check[access] = true;		// 일반 사용자의 글 수정 가능
												}



											}

										}else{						// 비로그인 사용자의 글
											$check[access] = true;
										}


									}


							}else{							// 일반 사용자 수정,삭제 권한


									if($mode_type == 1){
											if($writer_mode){
												$check[access] = true;

											}else{
												$_msg = "page;=;noauth;|;msg;=;권한이 없습니다.[u2];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
												$check[msg] = $_msg;
												$check[access] = false;
											}

									}else if($mode_type == 2){
										$_msg = "page;=;noauth;|;msg;=;로그인이 필요합니다.[u3];|;page;=;login;|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
										$check[msg] = $_msg;
										$check[access] = false;

									}else if($mode_type == 3){
										$_msg =  "page;=;noauth;|;msg;=;권한이 없습니다.[u4];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
										$check[msg] = $_msg;
										$check[access] = false;

									}else{
										if($_GET[upw] !=""){
											$upw_tmp = base64_decode($_GET[upw]);
											$upw_real = substr($upw_tmp, 8, strlen($upw_tmp)-8);
											if($db_upw == $upw_real){
												$check[access] = true;

											}else{
												$_msg = "page;=;upw;|;msg;=;암호가 틀렸습니다. 암호를 다시 입력해주세요.[u5];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
												$check[msg] = $_msg;
												$check[access] = false;

											}

										}else{
												$_msg = "page;=;upw;|;msg;=;암호를 입력해주세요.[u6];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
												$check[msg] = $_msg;
												$check[access] = false;
										}

									}

							}



					}else{
						if($sess_loginMode){
							$_msg = "page;=;noauth;|;msg;=;접근 권한이 없습니다.[u7];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
							$check[msg] = $_msg;
							$check[access] = false;

						}else{
							$_msg = "page;=;login;|;msg;=;로그인이 필요합니다.[u8];|;backUrl;=;".$_backUrl.";|;cancleUrl;=;".$_cancleUrl;
							$check[msg] = $_msg;
							$check[access] = false;
						}

					}

					// 공지를 수정할 권한이 있는지 미리 가져옴
					if($user_level <= intval($_level[auth_gongji])){
						$check[auth_gongji] = true;
					}

					// 글 수정/삭제 페이지에서 fileup 권한을 미리 가져옴
					if($user_level <= intval($_level[auth_fileup])){
						$check[auth_fileup] = true;
					}

					// 글 수정/삭제 페이지에서 filedown 권한을 미리 가져옴
					if($user_level <= intval($_level[auth_filedown])){
						$check[auth_filedown] = true;
					}

					return $check;

			

				


		}	// select case





	}	// end of function




	/*****************************************************************************************
		grant check
	*****************************************************************************************/
	function spamCheck($_spamDataArray, $_textData){
		$check[isok] = true;
		$check[word] = "";

		for($i=0; $i<count($_spamDataArray); $i++){
			$pattern = "[".$_spamDataArray[$i]."]";
			preg_match($pattern, $_textData, $matches, PREG_OFFSET_CAPTURE);
			if(trim($matches[0][0]) != ""){
				$check[isok] = false;
				$check[word] = $matches[0][0];
				$check[wherenum] = $matches[0][1];
				break;
			}

		}

		return $check;

	}




		
	/*****************************************************************************************
		문자열 자르기 함수
	*****************************************************************************************/
		function strcut($str,$len)
		{
			$newText = strip_tags(htmlspecialchars_decode(trim($str)));	// 텍스트만 잘라내므로 필요없는 테그 등은 모두 삭제한 상태에서 잘라낸다.
			$_len=0;
			$_txt="";

			for($i=0; $i<strlen($newText); $i++){	
				$tmp = mb_substr($newText, $i, 1, "utf-8");
				if(strlen($tmp) > 1){
					$tmpType = 2;
					$_len += 2;
				}else{
					$tmpType = 1;
					$_len += 1;
				}
				
				if($len > $_len)
					$_txt .= $tmp;
				else
					break;
			}

			if(strlen($newText) > strlen($_txt))
				$_txt .= "..";
			return $_txt;
		}




	/*****************************************************************************************
		해당된 날짜값을 오늘과 비교하여 최신글인지 알려줌
	*****************************************************************************************/
	function isNew($writedate){		// arrary[sMenu값] = boardKey값 형식으로 배열을 반환함
		$t_value = false;

		$_twritedate =  strtotime($writedate);
		$t_now = time();
		$t_yesterday = $t_now - (24 * 3600);	 // 1일 전의 글은 최신글로 함


		if($_twritedate >= $t_yesterday)
			$t_value = true;

		return $t_value;
		
	}



	/*****************************************************************************************
		파일 이름으로 아이콘 이미지와 확장자를 반환함
	*****************************************************************************************/
	function getFileIcon($filename){


		$hwpArr = array("hwp", "hwt");
		$pptArr = array("ppt", "pptx",  "pot", "potx");
		$htmlArr = array("html", "htm");
		$xlsArr = array("xls", "xlsx", "xlt", "xlsb", "csv");
		$pdfArr = array("pdf");
		$gifArr = array("gif", "jpg", "jpeg", "png", "bmp");
		$swfArr = array("swf", "fla", "flv");
		$mp3Arr = array("mp3", "wav", "mid", "pcm", "raw", "dbl");
		$docArr = array("doc", "docx", "dotx", "dot");
		$zipArr = array("zip", "alz", "rar", "arj");
		$aviArr = array("avi", "mpg", "mpeg", "asf", "wmv", "flv", "mp4");

		$icon = "file_etc.gif";

		$ext = getFileExtention($filename);
		if($ext !=""){
			if(in_array($ext, $hwpArr))
				$icon = "file_hwp.gif";
			else if(in_array($ext, $pptArr))
				$icon = "file_ppt.gif";
			else if(in_array($ext, $htmlArr))
				$icon = "file_html.gif";
			else if(in_array($ext, $xlsArr))
				$icon = "file_xls.gif";
			else if(in_array($ext, $pdfArr))
				$icon = "file_pdf.gif";
			else if(in_array($ext, $gifArr))
				$icon = "file_gif.gif";
			else if(in_array($ext, $swfArr))
				$icon = "file_swf.gif";
			else if(in_array($ext, $mp3Arr))
				$icon = "file_mp3.gif";
			else if(in_array($ext, $docArr))
				$icon = "file_doc.gif";
			else if(in_array($ext, $zipArr))
				$icon = "file_zip.gif";
			else if(in_array($ext, $aviArr))
				$icon = "file_avi.gif";
			else
				$icon = "file_etc.gif";

		}

		$iconData[icon] = $icon;
		$iconData[ext] = $ext;


		return $iconData;
		
	}



	/*****************************************************************************************
		xml 내용으로 된 컨텐츠를 배열로 반환함
	*****************************************************************************************/
	
	function xml_to_array( $xml_contents )
	{
		$parser = xml_parser_create();
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parse_into_struct( $parser, $xml_contents, $tags );
		xml_parser_free( $parser );
		
		$elements = array();
		$stack = array();
		foreach ( $tags as $tag )
		{
			$index = count( $elements );
			if ( $tag['type'] == "complete" || $tag['type'] == "open" )
			{
				$elements[$index] = array();
				$elements[$index]['name'] = $tag['tag'];
				$elements[$index]['attributes'] = $tag['attributes'];
				$elements[$index]['content'] = $tag['value'];
				
				if ( $tag['type'] == "open" )
				{    # push
					$elements[$index]['children'] = array();
					$stack[count($stack)] = &$elements;
					$elements = &$elements[$index]['children'];
				}
			}
			
			if ( $tag['type'] == "close" )
			{    # pop
				$elements = &$stack[count($stack) - 1];
				unset($stack[count($stack) - 1]);
			}
		}
		return $elements[0];
	}



	/*****************************************************************************************
		xml url 경로를 주면  xml 파일의 구조를 xml_to_array 함수를 통해서 배열로 반환함
	*****************************************************************************************/
	
	function xmlurl_to_array( $_xml_url ){

		if ($fp = fopen($_xml_url, 'r')) {
	   $content = '';
	   // keep reading until there's nothing left 
			while ($line = fread($fp, 1024)) {
				$content .= $line;
			}
	   }
	   return xml_to_array($content);
	}




	/*****************************************************************************************
		숫자로만 이루어진 전화번호를 자동으로 하이픈(-) 넣어주어서 반환 함
	*****************************************************************************************/
	function align_tel($telNo) { 
		$telNo = preg_replace('/[^\d\n]+/', '', $telNo); 
		if(substr($telNo,0,1)!="0" && strlen($telNo)>8) $telNo = "0".$telNo;
		$Pn3 = substr($telNo,-4);
		if(substr($telNo,0,2)=="01") $Pn1 =  substr($telNo,0,3);
		elseif(substr($telNo,0,2)=="02") $Pn1 =  substr($telNo,0,2);
		elseif(substr($telNo,0,1)=="0") $Pn1 =  substr($telNo,0,3);
		$Pn2 = substr($telNo,strlen($Pn1),-4);
		if(!$Pn1) return $Pn2."-".$Pn3; 
		else return $Pn1."-".$Pn2."-".$Pn3; 
	} 

	/*****************************************************************************************
	 주소값으로 LatLng 값을 반환 함수
	*****************************************************************************************/
	
	function getLatLng($area) {
	
		$key_local ="865f0262d2b936c87a5be954c90cdaddee8449bb";
	
		$_xml_url = "http://apis.daum.net/local/geo/addr2coord?apikey=".$key_local."&q=".urlencode($area)."&output=xml";
		//echo $_xml_url;
		$_xml_contents = xmlurl_to_array($_xml_url);
		//print_r($_xml_contents);
	
		$latitude = $_xml_contents[children][6][children][12][content];
		$longitude = $_xml_contents[children][6][children][11][content];
	
	
		$LatLng = $latitude.",".$longitude;
	
		return $LatLng;
	
	
	}
	
	/**
	 * 텍스트에서 지정된 length 만큼만 보여지고 나머지는 entity로 치환되는 함수
	 * ex) replace_text("홍길동", 2) => 홍길*
	 */
	function replace_text($text, $length=1, $entity="*"){
		$tmp = "";
		$return_text = "";
		$text_length = mb_strlen($text,"utf-8");
		for($i=0;$i<$text_length;$i++){
			$tmp = mb_substr($text,$i,1,"utf-8");
			if($i < ($length) ) $return_text .= $tmp;
			else $return_text .= $entity;
		}
	
		return $return_text;
	}
	
	
	
	function getFullTime($min,$sec){
		return $min * 60 + $sec;
	}
	
	
	
	
?>