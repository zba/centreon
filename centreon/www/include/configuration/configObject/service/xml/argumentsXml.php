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

    include_once "@CENTREON_ETC@/centreon.conf.php";

    require_once $centreon_path . "/www/class/centreonDB.class.php";
	require_once $centreon_path . "/www/class/centreonXML.class.php";

        /*
	 * Get session
	 */
	require_once ($centreon_path . "www/class/centreonSession.class.php");
	require_once ($centreon_path . "www/class/centreon.class.php");
	if(!isset($_SESSION['centreon'])) {
		CentreonSession::start();
	}

	if (isset($_SESSION['centreon'])) {
            $oreon = $_SESSION['centreon'];
	} else {
            exit;
	}

	/*
	 * Get language
	 */
	$locale = $oreon->user->get_lang();
	putenv("LANG=$locale");
	setlocale(LC_ALL, $locale);
	bindtextdomain("messages",  $centreon_path . "www/locale/");;
	bind_textdomain_codeset("messages", "UTF-8");
	textdomain("messages");
        
	/*
	 * Declare Function
	 */
	function myDecodeValue($arg) {
		$arg = str_replace('#S#', "/", $arg);
		$arg = str_replace('#BS#', "\\", $arg);
		return html_entity_decode($arg, ENT_QUOTES, "UTF-8");
	}

    /*
	 * start init db
	 */
	$db = new CentreonDB();
	$xml = new CentreonXML();

	$xml->startElement('root');
	$xml->startElement('main');
	$xml->writeElement('argLabel', _('Argument'));
	$xml->writeElement('argValue', _('Value'));
	$xml->writeElement('argExample', _('Example'));
	$xml->writeElement('noArgLabel', _('No argument found for this command'));
	$xml->endElement();

    if (isset($_GET['cmdId']) && isset($_GET['svcId']) && isset($_GET['svcTplId']) && isset($_GET['o'])) {

        $cmdId = CentreonDB::escape($_GET['cmdId']);
        $svcId = CentreonDB::escape($_GET['svcId']);
        $svcTplId = CentreonDB::escape($_GET['svcTplId']);
        $o = CentreonDB::escape($_GET['o']);

        $tab = array();
        if (!$cmdId && $svcTplId) {
            while (1) {
			    $query4 = "SELECT service_template_model_stm_id, command_command_id, command_command_id_arg FROM `service` WHERE service_id = '" . $svcTplId . "'";
			    $res4 = $db->query($query4);
			 	$row4 = $res4->fetchRow();
			 	if (isset($row4['command_command_id']) && $row4['command_command_id']) {
		 			$cmdId = $row4['command_command_id'];
		 			break;
			 	}
			 	if (!isset($row4['service_template_model_stm_id']) || !$row4['service_template_model_stm_id']) {
		 			break;
			 	}
			 	if (isset($tab[$row4['service_template_model_stm_id']])) {
                    break;
			 	}
			 	$svcTplId = $row4['service_template_model_stm_id'];
                $tab[$svcTplId] = 1;
            }
        }

        $argTab = array();

        $query2 = "SELECT command_line, command_example FROM command WHERE command_id = '".$cmdId."' LIMIT 1";
        $res2 = $db->query($query2);
        $row2 = $res2->fetchRow();
        $cmdLine = $row2['command_line'];
        preg_match_all("/\\\$(ARG[0-9]+)\\\$/", $cmdLine, $matches);
        foreach ($matches[1] as $key => $value) {
		    $argTab[$value] = $value;
		}
        $exampleTab = preg_split('/\!/', $row2['command_example']);
        if (is_array($exampleTab)) {
            foreach ($exampleTab as $key => $value) {
                $nbTmp = $key;
                $exampleTab['ARG'.$nbTmp] = $value;
                unset($exampleTab[$key]);
            }
        } else {
            $exampleTab = array();
        }

        $query3 = "SELECT command_command_id_arg " .
                  "FROM service " .
                  "WHERE service_id = '".$svcId."' LIMIT 1";
        $res3 = $db->query($query3);
        if ($res3->numRows()) {
            $row3 = $res3->fetchRow();
            $valueTab = preg_split('/(?<!\\\)\!/', $row3['command_command_id_arg']);
            if (is_array($valueTab)) {
                foreach($valueTab as $key => $value) {
                    $nbTmp = $key;
                    $valueTab['ARG'.$nbTmp] = $value;
                    unset($valueTab[$key]);
                }
            } else {
                $exampleTab = array();
            }
        }

		$query = "SELECT macro_name, macro_description " .
                 "FROM command_arg_description ".
                 "WHERE cmd_id = '".$cmdId."' ORDER BY macro_name" ;
        $res = $db->query($query);
        while ($row = $res->fetchRow()) {
            $argTab[$row['macro_name']] = $row['macro_description'];
        }
        $res->free();

        /*
         * Write XML
         */
        $style = 'list_two';
        $disabled = 0;
        $nbArg = 0;
        foreach ($argTab as $name => $description) {
            $style == 'list_one' ? $style = 'list_two' : $style = 'list_one';
            if ($o == "w") {
                $disabled = 1;
            }
            $xml->startElement('arg');
            $xml->writeElement('name', $name, false);
            $xml->writeElement('description', $description, false);
            $xml->writeElement('value', isset($valueTab[$name]) ? $valueTab[$name] : "", false);
            $xml->writeElement('example', isset($exampleTab[$name]) ? myDecodeValue($exampleTab[$name]) : "", false);
            $xml->writeElement('style', $style);
            $xml->writeElement('disabled', $disabled);
            $xml->endElement();
            $nbArg++;
        }
    }
    $xml->writeElement('nbArg', $nbArg);
	$xml->endElement();
    header('Content-Type: text/xml');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Cache-Control: no-cache, must-revalidate');
	$xml->output();
?>
