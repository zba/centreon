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
 
	if (!isset ($oreon))
		exit ();
	
	function testHostGroupDependencyExistence ($name = NULL)	{
		global $pearDB;
		global $form;
		$id = NULL;
		if (isset($form))
			$id = $form->getSubmitValue('dep_id');
		$DBRESULT =& $pearDB->query("SELECT dep_name, dep_id FROM dependency WHERE dep_name = '".htmlentities($name, ENT_QUOTES)."'");
		$dep =& $DBRESULT->fetchRow();
		#Modif case
		if ($DBRESULT->numRows() >= 1 && $dep["dep_id"] == $id)	
			return true;
		#Duplicate entry
		else if ($DBRESULT->numRows() >= 1 && $dep["dep_id"] != $id)
			return false;
		else
			return true;
	}
	
	function testHostGroupDependencyCycle ($childs = NULL)	{
		global $pearDB;
		global $form;
		$parents = array();
		$childs = array();
		if (isset($form))	{
			$parents = $form->getSubmitValue('dep_hgParents');
			$childs = $form->getSubmitValue('dep_hgChilds');
			$childs =& array_flip($childs);
		}
		foreach ($parents as $parent)
			if (array_key_exists($parent, $childs))
				return false;
		return true;
	}

	function deleteHostGroupDependencyInDB ($dependencies = array())	{
		global $pearDB, $oreon;
		foreach($dependencies as $key=>$value)		{
			$DBRESULT2 =& $pearDB->query("SELECT dep_name FROM `dependency` WHERE `dep_id` = '".$key."' LIMIT 1");
			$row = $DBRESULT2->fetchRow();
			
			$DBRESULT =& $pearDB->query("DELETE FROM dependency WHERE dep_id = '".$key."'");
			$oreon->CentreonLogAction->insertLog("hostgroup dependency", $key, $row['dep_name'], "d");
		}
	}
	
	function multipleHostGroupDependencyInDB ($dependencies = array(), $nbrDup = array())	{
		foreach($dependencies as $key=>$value)	{
			global $pearDB, $oreon;
			$DBRESULT =& $pearDB->query("SELECT * FROM dependency WHERE dep_id = '".$key."' LIMIT 1");
			$row = $DBRESULT->fetchRow();
			$row["dep_id"] = '';
			for ($i = 1; $i <= $nbrDup[$key]; $i++)	{
				$val = null;
				foreach ($row as $key2=>$value2)	{
					$key2 == "dep_name" ? ($dep_name = $value2 = $value2."_".$i) : null;
					$val ? $val .= ($value2!=NULL?(", '".$value2."'"):", NULL") : $val .= ($value2!=NULL?("'".$value2."'"):"NULL");
					if ($key2 != "dep_id")
						$fields[$key2] = $value2;
					$fields["dep_name"] = $dep_name;
				}
				if (testHostGroupDependencyExistence($dep_name))	{
					$val ? $rq = "INSERT INTO dependency VALUES (".$val.")" : $rq = null;
					$DBRESULT =& $pearDB->query($rq);
					$DBRESULT =& $pearDB->query("SELECT MAX(dep_id) FROM dependency");
					$maxId =& $DBRESULT->fetchRow();
					if (isset($maxId["MAX(dep_id)"]))	{
						$DBRESULT =& $pearDB->query("SELECT DISTINCT hostgroup_hg_id FROM dependency_hostgroupParent_relation WHERE dependency_dep_id = '".$key."'");
						$fields["dep_hgParents"] = "";
						while($hg =& $DBRESULT->fetchRow())	{
							$DBRESULT2 =& $pearDB->query("INSERT INTO dependency_hostgroupParent_relation VALUES ('', '".$maxId["MAX(dep_id)"]."', '".$hg["hostgroup_hg_id"]."')");
							$fields["dep_hgParents"] .= $hg["hostgroup_hg_id"] . ",";
						}
						$fields["dep_hgParents"] = trim($fields["dep_hgParents"], ",");
						$DBRESULT->free();
						$DBRESULT =& $pearDB->query("SELECT DISTINCT hostgroup_hg_id FROM dependency_hostgroupChild_relation WHERE dependency_dep_id = '".$key."'");
						$fields["dep_hgChilds"] = "";
						while($hg =& $DBRESULT->fetchRow())	{
							$DBRESULT2 =& $pearDB->query("INSERT INTO dependency_hostgroupChild_relation VALUES ('', '".$maxId["MAX(dep_id)"]."', '".$hg["hostgroup_hg_id"]."')");
							$fields["dep_hgChilds"] .= $hg["hostgroup_hg_id"] . ",";
						}
						$fields["dep_hgChilds"] = trim($fields["dep_hgChilds"], ",");
						$DBRESULT->free();
						$oreon->CentreonLogAction->insertLog("hostgroup dependency", $maxId["MAX(dep_id)"], $dep_name, "a", $fields);
					}
				}
			}
		}
	}
	
	function updateHostGroupDependencyInDB ($dep_id = NULL)	{
		if (!$dep_id) exit();
		updateHostGroupDependency($dep_id);
		updateHostGroupDependencyHostGroupParents($dep_id);
		updateHostGroupDependencyHostGroupChilds($dep_id);
	}	
	
	function insertHostGroupDependencyInDB ($ret = array())	{
		$dep_id = insertHostGroupDependency($ret);
		updateHostGroupDependencyHostGroupParents($dep_id, $ret);
		updateHostGroupDependencyHostGroupChilds($dep_id, $ret);
		return ($dep_id);
	}
	
	function insertHostGroupDependency($ret = array())	{
		global $form;
		global $pearDB, $oreon;
		if (!count($ret))
			$ret = $form->getSubmitValues();
		$rq = "INSERT INTO dependency ";
		$rq .= "(dep_name, dep_description, inherits_parent, execution_failure_criteria, notification_failure_criteria, dep_comment) ";
		$rq .= "VALUES (";
		isset($ret["dep_name"]) && $ret["dep_name"] != NULL ? $rq .= "'".htmlentities($ret["dep_name"], ENT_QUOTES)."', " : $rq .= "NULL, ";
		isset($ret["dep_description"]) && $ret["dep_description"] != NULL ? $rq .= "'".htmlentities($ret["dep_description"], ENT_QUOTES)."', " : $rq .= "NULL, ";
		isset($ret["inherits_parent"]["inherits_parent"]) && $ret["inherits_parent"]["inherits_parent"] != NULL ? $rq .= "'".$ret["inherits_parent"]["inherits_parent"]."', " : $rq .= "NULL, ";
		isset($ret["execution_failure_criteria"]) && $ret["execution_failure_criteria"] != NULL ? $rq .= "'".implode(",", array_keys($ret["execution_failure_criteria"]))."', " : $rq .= "NULL, ";
		isset($ret["notification_failure_criteria"]) && $ret["notification_failure_criteria"] != NULL ? $rq .= "'".implode(",", array_keys($ret["notification_failure_criteria"]))."', " : $rq .= "NULL, ";
		isset($ret["dep_comment"]) && $ret["dep_comment"] != NULL ? $rq .= "'".htmlentities($ret["dep_comment"], ENT_QUOTES)."' " : $rq .= "NULL ";
		$rq .= ")";
		$DBRESULT =& $pearDB->query($rq);
		$DBRESULT =& $pearDB->query("SELECT MAX(dep_id) FROM dependency");
		$dep_id = $DBRESULT->fetchRow();
		$fields["dep_name"] = htmlentities($ret["dep_name"], ENT_QUOTES);
		$fields["dep_description"] = htmlentities($ret["dep_description"], ENT_QUOTES);
		$fields["inherits_parent"] = $ret["inherits_parent"]["inherits_parent"];
		$fields["execution_failure_criteria"] = implode(",", array_keys($ret["execution_failure_criteria"]));
		$fields["notification_failure_criteria"] = implode(",", array_keys($ret["notification_failure_criteria"]));
		$fields["dep_comment"] = htmlentities($ret["dep_comment"], ENT_QUOTES);
		$fields["dep_hgParents"] = "";
		if (isset($ret["dep_hgParents"]))
			$fields["dep_hgParents"] = implode(",", $ret["dep_hgParents"]);
		$fields["dep_hgChilds"] = "";
		if (isset($ret["dep_hgChilds"]))
			$fields["dep_hgChilds"] = implode(",", $ret["dep_hgChilds"]);
		$oreon->CentreonLogAction->insertLog("hostgroup dependency", $dep_id["MAX(dep_id)"], htmlentities($ret["dep_name"], ENT_QUOTES), "a", $fields);
		return ($dep_id["MAX(dep_id)"]);
	}
	
	function updateHostGroupDependency($dep_id = null)	{
		if (!$dep_id) exit();
		global $form;
		global $pearDB, $oreon;
		$ret = array();
		$ret = $form->getSubmitValues();
		$rq = "UPDATE dependency SET ";
		$rq .= "dep_name = ";
		isset($ret["dep_name"]) && $ret["dep_name"] != NULL ? $rq .= "'".htmlentities($ret["dep_name"], ENT_QUOTES)."', " : $rq .= "NULL, ";
		$rq .= "dep_description = ";
		isset($ret["dep_description"]) && $ret["dep_description"] != NULL ? $rq .= "'".htmlentities($ret["dep_description"], ENT_QUOTES)."', " : $rq .= "NULL, ";
		$rq .= "inherits_parent = ";
		isset($ret["inherits_parent"]["inherits_parent"]) && $ret["inherits_parent"]["inherits_parent"] != NULL ? $rq .= "'".$ret["inherits_parent"]["inherits_parent"]."', " : $rq .= "NULL, ";
		$rq .= "execution_failure_criteria = ";
		isset($ret["execution_failure_criteria"]) && $ret["execution_failure_criteria"] != NULL ? $rq .= "'".implode(",", array_keys($ret["execution_failure_criteria"]))."', " : $rq .= "NULL, ";
		$rq .= "notification_failure_criteria = ";
		isset($ret["notification_failure_criteria"]) && $ret["notification_failure_criteria"] != NULL ? $rq .= "'".implode(",", array_keys($ret["notification_failure_criteria"]))."', " : $rq .= "NULL, ";
		$rq .= "dep_comment = ";
		isset($ret["dep_comment"]) && $ret["dep_comment"] != NULL ? $rq .= "'".htmlentities($ret["dep_comment"], ENT_QUOTES)."' " : $rq .= "NULL ";
		$rq .= "WHERE dep_id = '".$dep_id."'";
		$DBRESULT =& $pearDB->query($rq);
		$fields["dep_name"] = htmlentities($ret["dep_name"], ENT_QUOTES);
		$fields["dep_description"] = htmlentities($ret["dep_description"], ENT_QUOTES);
		$fields["inherits_parent"] = $ret["inherits_parent"]["inherits_parent"];
		$fields["execution_failure_criteria"] = implode(",", array_keys($ret["execution_failure_criteria"]));
		$fields["notification_failure_criteria"] = implode(",", array_keys($ret["notification_failure_criteria"]));
		$fields["dep_comment"] = htmlentities($ret["dep_comment"], ENT_QUOTES);
		$fields["dep_hgParents"] = "";
		if (isset($ret["dep_hgParents"]))
			$fields["dep_hgParents"] = implode(",", $ret["dep_hgParents"]);
		$fields["dep_hgChilds"] = "";
		if (isset($ret["dep_hgChilds"]))
			$fields["dep_hgChilds"] = implode(",", $ret["dep_hgChilds"]);
		$oreon->CentreonLogAction->insertLog("hostgroup dependency", $dep_id, htmlentities($ret["dep_name"], ENT_QUOTES), "c", $fields);
	}
		
	function updateHostGroupDependencyHostGroupParents($dep_id = null, $ret = array())	{
		if (!$dep_id) exit();
		global $form;
		global $pearDB;
		$rq = "DELETE FROM dependency_hostgroupParent_relation ";
		$rq .= "WHERE dependency_dep_id = '".$dep_id."'";
		$DBRESULT =& $pearDB->query($rq);
		if (isset($ret["dep_hgParents"]))
			$ret = $ret["dep_hgParents"];
		else
			$ret = $form->getSubmitValue("dep_hgParents");
		for($i = 0; $i < count($ret); $i++)	{
			$rq = "INSERT INTO dependency_hostgroupParent_relation ";
			$rq .= "(dependency_dep_id, hostgroup_hg_id) ";
			$rq .= "VALUES ";
			$rq .= "('".$dep_id."', '".$ret[$i]."')";
			$DBRESULT =& $pearDB->query($rq);
		}
	}
		
	function updateHostGroupDependencyHostGroupChilds($dep_id = null, $ret = array())	{
		if (!$dep_id) exit();
		global $form;
		global $pearDB;
		$rq = "DELETE FROM dependency_hostgroupChild_relation ";
		$rq .= "WHERE dependency_dep_id = '".$dep_id."'";
		$DBRESULT =& $pearDB->query($rq);
		if (isset($ret["dep_hgChilds"]))
			$ret = $ret["dep_hgChilds"];
		else
			$ret = $form->getSubmitValue("dep_hgChilds");
		for($i = 0; $i < count($ret); $i++)	{
			$rq = "INSERT INTO dependency_hostgroupChild_relation ";
			$rq .= "(dependency_dep_id, hostgroup_hg_id) ";
			$rq .= "VALUES ";
			$rq .= "('".$dep_id."', '".$ret[$i]."')";
			$DBRESULT =& $pearDB->query($rq);
		}
	}
?>