<?php
/*
 * Copyright 2015 Centreon (http://www.centreon.com/)
 * 
 * Centreon is a full-fledged industry-strength solution that meets 
 * the needs in IT infrastructure and application monitoring for 
 * service performance.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *    http://www.apache.org/licenses/LICENSE-2.0  
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * For more information : contact@centreon.com
 * 
 */
 
 	if (!isset ($oreon))
		exit ();
	
	isset($_GET["graph_id"]) ? $cG = $_GET["graph_id"] : $cG = NULL;
	isset($_POST["graph_id"]) ? $cP = $_POST["graph_id"] : $cP = NULL;
	$cG ? $graph_id = $cG : $graph_id = $cP;

	isset($_GET["select"]) ? $cG = $_GET["select"] : $cG = NULL;
	isset($_POST["select"]) ? $cP = $_POST["select"] : $cP = NULL;
	$cG ? $select = $cG : $select = $cP;

	isset($_GET["dupNbr"]) ? $cG = $_GET["dupNbr"] : $cG = NULL;
	isset($_POST["dupNbr"]) ? $cP = $_POST["dupNbr"] : $cP = NULL;
	$cG ? $dupNbr = $cG : $dupNbr = $cP;
		
	/*
	 * Pear library
	 */
	require_once "HTML/QuickForm.php";
	require_once 'HTML/QuickForm/advmultiselect.php';
	require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
	
	/*
	 * Path to the configuration dir
	 */
	$path = "./include/views/graphs/graphTemplates/";
	
	/*
	 * PHP functions
	 */
	require_once $path."DB-Func.php";
	require_once "./include/common/common-Func.php";
	
	switch ($o)	{
		case "a" : 
			require_once $path."formGraphTemplate.php"; 
			break; #Add a Graph Template
		case "w" : 
			require_once $path."formGraphTemplate.php"; 
			break; #Watch aGraph Template
		case "c" : 
			require_once $path."formGraphTemplate.php"; 
			break; #Modify a Graph Template
		case "s" : 
			enableGraphTemplateInDB($lca_id); 
			require_once $path."listGraphTemplates.php"; 
			break; #Activate a Graph Template
		case "u" : 
			disableGraphTemplateInDB($lca_id); 
			require_once $path."listGraphTemplates.php"; 
			break; #Desactivate a Graph Template
		case "m" : 
			multipleGraphTemplateInDB(isset($select) ? $select : array(), $dupNbr); 
			require_once $path."listGraphTemplates.php"; 
			break; #Duplicate n Graph Templates
		case "d" : 
			deleteGraphTemplateInDB(isset($select) ? $select : array()); 
			require_once $path."listGraphTemplates.php"; 
			break; #Delete n Graph Templates
		default : 
			require_once $path."listGraphTemplates.php" ; 
			break;
	}
?>