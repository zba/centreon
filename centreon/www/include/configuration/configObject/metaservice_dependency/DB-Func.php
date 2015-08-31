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
		
	function testExistence ($name = NULL)	{
		global $pearDB;
		global $form;

		CentreonDependency::purgeObsoleteDependencies($pearDB);

		$id = NULL;
		if (isset($form))
			$id = $form->getSubmitValue('dep_id');
		$DBRESULT = $pearDB->query("SELECT dep_name, dep_id FROM dependency WHERE dep_name = '".htmlentities($name, ENT_QUOTES, "UTF-8")."'");
		$dep = $DBRESULT->fetchRow();
		#Modif case
		if ($DBRESULT->numRows() >= 1 && $dep["dep_id"] == $id)	
			return true;
		#Duplicate entry
		else if ($DBRESULT->numRows() >= 1 && $dep["dep_id"] != $id)
			return false;
		else
			return true;
	}
	
	function testCycle ($childs = NULL)	{
		global $pearDB;
		global $form;
		$parents = array();
		$childs = array();
		if (isset($form))	{
			$parents = $form->getSubmitValue('dep_msParents');
			$childs = $form->getSubmitValue('dep_msChilds');
			$childs = array_flip($childs);
		}
		foreach ($parents as $parent)
			if (array_key_exists($parent, $childs))
				return false;
		return true;
	}

	function deleteMetaServiceDependencyInDB ($dependencies = array())	{
		global $pearDB;
		foreach($dependencies as $key=>$value)		{
			$DBRESULT = $pearDB->query("DELETE FROM dependency WHERE dep_id = '".$key."'");
		}
	}
	
	function multipleMetaServiceDependencyInDB ($dependencies = array(), $nbrDup = array())	{
		foreach($dependencies as $key=>$value)	{
			global $pearDB;
			$DBRESULT = $pearDB->query("SELECT * FROM dependency WHERE dep_id = '".$key."' LIMIT 1");
			$row = $DBRESULT->fetchRow();
			$row["dep_id"] = '';
			for ($i = 1; $i <= $nbrDup[$key]; $i++)	{
				$val = null;
				foreach ($row as $key2=>$value2)	{
					$key2 == "dep_name" ? ($dep_name = $value2 = $value2."_".$i) : null;
					$val ? $val .= ($value2!=NULL?(", '".$value2."'"):", NULL") : $val .= ($value2!=NULL?("'".$value2."'"):"NULL");
				}
				if (testExistence($dep_name))	{
					$val ? $rq = "INSERT INTO dependency VALUES (".$val.")" : $rq = null;
					$DBRESULT = $pearDB->query($rq);
					$DBRESULT = $pearDB->query("SELECT MAX(dep_id) FROM dependency");
					$maxId = $DBRESULT->fetchRow();
					if (isset($maxId["MAX(dep_id)"]))	{
						$DBRESULT = $pearDB->query("SELECT DISTINCT meta_service_meta_id FROM dependency_metaserviceParent_relation WHERE dependency_dep_id = '".$key."'");
						while($ms = $DBRESULT->fetchRow())	{
							$DBRESULT2 = $pearDB->query("INSERT INTO dependency_metaserviceParent_relation VALUES ('', '".$maxId["MAX(dep_id)"]."', '".$ms["meta_service_meta_id"]."')");
						}
						$DBRESULT->free();
						$DBRESULT = $pearDB->query("SELECT DISTINCT meta_service_meta_id FROM dependency_metaserviceChild_relation WHERE dependency_dep_id = '".$key."'");
						while($ms = $DBRESULT->fetchRow()){
							$DBRESULT2 = $pearDB->query("INSERT INTO dependency_metaserviceChild_relation VALUES ('', '".$maxId["MAX(dep_id)"]."', '".$ms["meta_service_meta_id"]."')");
						}
						$DBRESULT->free();
					}
				}
			}
		}
	}
	
	function updateMetaServiceDependencyInDB ($dep_id = NULL)	{
		if (!$dep_id) exit();
		updateMetaServiceDependency($dep_id);
		updateMetaServiceDependencyMetaServiceParents($dep_id);
		updateMetaServiceDependencyMetaServiceChilds($dep_id);
	}	
	
	function insertMetaServiceDependencyInDB ()	{
		$dep_id = insertMetaServiceDependency();
		updateMetaServiceDependencyMetaServiceParents($dep_id);
		updateMetaServiceDependencyMetaServiceChilds($dep_id);
		return ($dep_id);
	}
	
	function insertMetaServiceDependency()	{
		global $form;
		global $pearDB;
		$ret = array();
		$ret = $form->getSubmitValues();
		$rq = "INSERT INTO dependency ";
		$rq .= "(dep_name, dep_description, inherits_parent, execution_failure_criteria, notification_failure_criteria, dep_comment) ";
		$rq .= "VALUES (";
		isset($ret["dep_name"]) && $ret["dep_name"] != NULL ? $rq .= "'".htmlentities($ret["dep_name"], ENT_QUOTES, "UTF-8")."', " : $rq .= "NULL, ";
		isset($ret["dep_description"]) && $ret["dep_description"] != NULL ? $rq .= "'".htmlentities($ret["dep_description"], ENT_QUOTES, "UTF-8")."', " : $rq .= "NULL, ";
		isset($ret["inherits_parent"]["inherits_parent"]) && $ret["inherits_parent"]["inherits_parent"] != NULL ? $rq .= "'".$ret["inherits_parent"]["inherits_parent"]."', " : $rq .= "NULL, ";
		isset($ret["execution_failure_criteria"]) && $ret["execution_failure_criteria"] != NULL ? $rq .= "'".implode(",", array_keys($ret["execution_failure_criteria"]))."', " : $rq .= "NULL, ";
		isset($ret["notification_failure_criteria"]) && $ret["notification_failure_criteria"] != NULL ? $rq .= "'".implode(",", array_keys($ret["notification_failure_criteria"]))."', " : $rq .= "NULL, ";
		isset($ret["dep_comment"]) && $ret["dep_comment"] != NULL ? $rq .= "'".htmlentities($ret["dep_comment"], ENT_QUOTES, "UTF-8")."' " : $rq .= "NULL ";
		$rq .= ")";
		$DBRESULT = $pearDB->query($rq);
		$DBRESULT = $pearDB->query("SELECT MAX(dep_id) FROM dependency");
		$dep_id = $DBRESULT->fetchRow();
		return ($dep_id["MAX(dep_id)"]);
	}
	
	function updateMetaServiceDependency($dep_id = null)	{
		if (!$dep_id) exit();
		global $form;
		global $pearDB;
		$ret = array();
		$ret = $form->getSubmitValues();
		$rq = "UPDATE dependency SET ";
		$rq .= "dep_name = ";
		isset($ret["dep_name"]) && $ret["dep_name"] != NULL ? $rq .= "'".htmlentities($ret["dep_name"], ENT_QUOTES, "UTF-8")."', " : $rq .= "NULL, ";
		$rq .= "dep_description = ";
		isset($ret["dep_description"]) && $ret["dep_description"] != NULL ? $rq .= "'".htmlentities($ret["dep_description"], ENT_QUOTES, "UTF-8")."', " : $rq .= "NULL, ";
		$rq .= "inherits_parent = ";
		isset($ret["inherits_parent"]["inherits_parent"]) && $ret["inherits_parent"]["inherits_parent"] != NULL ? $rq .= "'".$ret["inherits_parent"]["inherits_parent"]."', " : $rq .= "NULL, ";
		$rq .= "execution_failure_criteria = ";
		isset($ret["execution_failure_criteria"]) && $ret["execution_failure_criteria"] != NULL ? $rq .= "'".implode(",", array_keys($ret["execution_failure_criteria"]))."', " : $rq .= "NULL, ";
		$rq .= "notification_failure_criteria = ";
		isset($ret["notification_failure_criteria"]) && $ret["notification_failure_criteria"] != NULL ? $rq .= "'".implode(",", array_keys($ret["notification_failure_criteria"]))."', " : $rq .= "NULL, ";
		$rq .= "dep_comment = ";
		isset($ret["dep_comment"]) && $ret["dep_comment"] != NULL ? $rq .= "'".htmlentities($ret["dep_comment"], ENT_QUOTES, "UTF-8")."' " : $rq .= "NULL ";
		$rq .= "WHERE dep_id = '".$dep_id."'";
		$DBRESULT = $pearDB->query($rq);
	}
		
	function updateMetaServiceDependencyMetaServiceParents($dep_id = null)	{
		if (!$dep_id) exit();
		global $form;
		global $pearDB;
		$rq = "DELETE FROM dependency_metaserviceParent_relation ";
		$rq .= "WHERE dependency_dep_id = '".$dep_id."'";
		$DBRESULT = $pearDB->query($rq);
		$ret = array();
		$ret = CentreonUtils::mergeWithInitialValues($form, 'dep_msParents');
		for($i = 0; $i < count($ret); $i++)	{
			$rq = "INSERT INTO dependency_metaserviceParent_relation ";
			$rq .= "(dependency_dep_id, meta_service_meta_id) ";
			$rq .= "VALUES ";
			$rq .= "('".$dep_id."', '".$ret[$i]."')";
			$DBRESULT = $pearDB->query($rq);
		}
	}
		
	function updateMetaServiceDependencyMetaServiceChilds($dep_id = null)	{
		if (!$dep_id) exit();
		global $form;
		global $pearDB;
		$rq = "DELETE FROM dependency_metaserviceChild_relation ";
		$rq .= "WHERE dependency_dep_id = '".$dep_id."'";
		$DBRESULT = $pearDB->query($rq);
		$ret = array();
		$ret = CentreonUtils::mergeWithInitialValues($form, 'dep_msChilds');
		for($i = 0; $i < count($ret); $i++)	{
			$rq = "INSERT INTO dependency_metaserviceChild_relation ";
			$rq .= "(dependency_dep_id, meta_service_meta_id) ";
			$rq .= "VALUES ";
			$rq .= "('".$dep_id."', '".$ret[$i]."')";
			$DBRESULT = $pearDB->query($rq);
		}
	}
?>
