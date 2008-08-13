<?php
/*
 * Centreon is developped with GPL Licence 2.0 :
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 * Developped by : Julien Mathis - Romain Le Merlus 
 * 
 * The Software is provided to you AS IS and WITH ALL FAULTS.
 * Centreon makes no representation and gives no warranty whatsoever,
 * whether express or implied, and without limitation, with regard to the quality,
 * any particular or intended purpose of the Software found on the Centreon web site.
 * In no event will Centreon be liable for any direct, indirect, punitive, special,
 * incidental or consequential damages however they may arise and even if Centreon has
 * been previously advised of the possibility of such damages.
 * 
 * For information : contact@centreon.com
 */

	if (!isset($oreon))
		exit();

	require_once './class/other.class.php';
	include_once("./include/monitoring/common-Func.php");
	include_once("./include/monitoring/external_cmd/cmd.php");

	#Pear library
	require_once "HTML/QuickForm.php";
	require_once 'HTML/QuickForm/advmultiselect.php';
	require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

	$DBRESULT =& $pearDB->query("SELECT ndo_activate FROM general_opt LIMIT 1");
	# Set base value
	$gopt = array_map("myDecode", $DBRESULT->fetchRow());

	$ndo = $gopt["ndo_activate"];


	if ($ndo)
		$path = "./include/monitoring/status/status-ndo/";
	else{
		$path = "./include/monitoring/status/status-log/";
	}

	$pathDetails = "./include/monitoring/objectDetails/";

	if ($ndo){
		include_once("./DBNDOConnect.php");
	
		if (preg_match("/connect\ failed/", $pearDBndo->toString(), $str)) 
			print "<div class='msg'>"._("Connection Error to NDO DataBase ! \n")."</div>";
		else {
			if ($err_msg = table_not_exists("centreon_acl")) 
					print "<div class='msg'>"._("Warning: ").$err_msg."</div>";
			switch ($o)	{
				case "hg" 	: require_once($path."hostGroup.php"); break;
				case "hgpb" : require_once($path."hostGroup.php"); break;
				case "hgd" 	: require_once($pathDetails."hostgroupDetails.php"); break;
				default 	: require_once($path."hostGroup.php"); break;
			}
		}
	} else {
		include("./include/monitoring/status/resume.php");
		switch ($o)	{
			case "hg" 	: require_once($path."hostgroup.php"); break;
			case "hgpb" : require_once($path."hostgroup_problem.php"); break;
			case "hgd" 	: require_once($pathDetails."hostgroupDetails.php"); break;
			default 	: require_once($path."hostgroup.php"); break;
		}
	}
?>