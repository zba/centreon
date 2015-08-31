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

	if (!isset($oreon)) {
		exit();
	}
	
	$path = "./include/options/session/";	
	
	require_once "./include/common/common-Func.php";
	require_once "./class/centreonMsg.class.php";
		
	if (isset($_GET["o"]) && $_GET["o"] == "k"){
		$pearDB->query("DELETE FROM session WHERE session_id = '".$pearDB->escape($_GET["session_id"])."'");
		$msg = new CentreonMsg();
		$msg->setTextStyle("bold");
		$msg->setText(_("User kicked"));
		$msg->setTimeOut("3");
	}	

	/*
	 * Smarty template Init
	 */
	$tpl = new Smarty();
	$tpl = initSmartyTpl($path, $tpl);

	$session_data = array();
	$res = $pearDB->query("SELECT session.*, contact_name, contact_admin FROM session, contact WHERE contact_id = user_id ORDER BY contact_name, contact_admin");
	for ($cpt = 0;$r = $res->fetchRow();$cpt++){

		$session_data[$cpt] = array();
		if ($cpt % 2) {
			$session_data[$cpt]["class"] = "list_one";
		} else {
			$session_data[$cpt]["class"] = "list_two";
		} 
		
		$session_data[$cpt]["user_id"] = $r["user_id"];
		$session_data[$cpt]["user_alias"] = $r["contact_name"];
		$session_data[$cpt]["admin"] = $r["contact_admin"];
		$session_data[$cpt]["ip_address"] = $r["ip_address"];
		$session_data[$cpt]["last_reload"] = date("H:i:s", $r["last_reload"]);
		
		$resCP = $pearDB->query("SELECT topology_name, topology_icone, topology_page, topology_url_opt FROM topology WHERE topology_page = '".$r["current_page"]."'");
		$rCP = $resCP->fetchRow();
		
		$session_data[$cpt]["current_page"] = $r["current_page"].$rCP["topology_url_opt"];
        if ($rCP['topology_name'] != '') {
		    $session_data[$cpt]["topology_name"] = _($rCP["topology_name"]);
        } else {
		    $session_data[$cpt]["topology_name"] = $rCP["topology_name"];
        }
		if ($rCP["topology_icone"])
			$session_data[$cpt]["topology_icone"] = "<img src='".$rCP["topology_icone"]."'>";
		else
			$session_data[$cpt]["topology_icone"] = "&nbsp;";
		$session_data[$cpt]["actions"] = "<a href='./main.php?p=$p&o=k&session_id=".$r["session_id"]."'><img src='./img/icones/16x16/flash.gif' border='0' alt='"._("Kick User")."' title='"._("Kick User")."'></a>";

	}
	
	if (isset($msg)) {
		$tpl->assign("msg", $msg);
	}
			
	$tpl->assign("session_data", $session_data);
	$tpl->assign("wi_user", _("Users"));
	$tpl->assign("wi_where", _("Position"));
	$tpl->assign("wi_last_req", _("Last request"));
	$tpl->assign("distant_location", _("IP Address"));
	
	$tpl->display("connected_user.ihtml");
?>
