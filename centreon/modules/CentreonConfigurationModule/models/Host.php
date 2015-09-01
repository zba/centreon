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

namespace CentreonConfiguration\Models;

use Centreon\Internal\Di;
use Centreon\Models\CentreonBaseModel;
use CentreonConfiguration\Models\Service;
use CentreonConfiguration\Models\Relation\Host\Service as HostServiceRelation;
use CentreonConfiguration\Models\Relation\Host\Hosttemplate as HostHosttemplateRelation;
use CentreonConfiguration\Repository\HostRepository;
use CentreonConfiguration\Repository\ServiceRepository;

/**
 * Used for interacting with hosts
 *
 * @author sylvestre
 */
class Host extends CentreonBaseModel
{
    protected static $table = "cfg_hosts";
    protected static $primaryKey = "host_id";
    protected static $uniqueLabelField = "host_name";
    protected static $slugField        = "host_slug";
    protected static $relations = array(
        "\CentreonConfiguration\Models\Relation\Host\Service",
        "\CentreonConfiguration\Models\Relation\Host\Hostparents",
        "\CentreonConfiguration\Models\Relation\Host\Hostchildren"
    );
    
    /*
    * @relation \CentreonConfiguration\Models\Relation\Host\Service  
    */
    //protected $services;
    
    /*
    * @relation \CentreonConfiguration\Models\Relation\Host\Hostparents
    */
    //protected $hostparents;

    /*
    * @relation \CentreonConfiguration\Models\Relation\Host\Hostchildren
    */
    //protected $hostchildrens;
    
    /*
    * @simple command_command_id CentreonConfiguration\Models\Command
    */
    //protected $command;

    /*
     * @many CentreonConfiguration\Models\Test host_id
     */
    //protected $test;
    
    
    
    protected static $aclResourceType = 1;

    /*protected static $basicFilters = array(
        'host_register' => '1',
    );*/
    
    /**
     * Used for inserting object into database
     *
     * @param array $params
     * @return int
     */
    public static function insert($params = array())
    {
        $params['host_register'] = '1';
        $db = Di::getDefault()->get('db_centreon');
        $sql = "INSERT INTO " . static::$table;
        $sqlFields = "";
        $sqlValues = "";
        $sqlParams = array();
        $not_null_attributes = array();
        $is_int_attribute = array();
        static::setAttributeProps($params, $not_null_attributes, $is_int_attribute);

        foreach ($params as $key => $value) {
            if ($key == static::$primaryKey || is_null($value)) {
                continue;
            }
            if ($sqlFields != "") {
                $sqlFields .= ",";
            }
            if ($sqlValues != "") {
                $sqlValues .= ",";
            }
            $sqlFields .= $key;
            $sqlValues .= "?";
            if ($value === "" && !isset($not_null_attributes[$key])) {
                $value = null;
            } elseif (!is_numeric($value) && isset($is_int_attribute[$key])) {
                $value = null;
            }
            $type = \PDO::PARAM_STR;
            if (is_null($value)) {
                $type = \PDO::PARAM_NULL;
            }
            $sqlParams[] = array('value' => trim($value), 'type' => $type);
            
            
            // Custom macros
            
            
            
        }
        
        
        if ($sqlFields && $sqlValues) {
            $sql .= "(".$sqlFields.") VALUES (".$sqlValues.")";
            $stmt = $db->prepare($sql);
            $i = 1;
            foreach ($sqlParams as $v) {
                $stmt->bindValue($i, $v['value'], $v['type']);
                $i++;
            }
            $stmt->execute();
            return $db->lastInsertId(static::$table, static::$primaryKey);
        }
        return null;
    }

    /**
     * 
     * @param type $parameterNames
     * @param type $count
     * @param type $offset
     * @param type $order
     * @param type $sort
     * @param array $filters
     * @param type $filterType
     * @return type
     */
    public static function getList(
        $parameterNames = "*",
        $count = -1,
        $offset = 0,
        $order = null,
        $sort = "ASC",
        $filters = array(),
        $filterType = "OR",
        $tablesString = null,
        $staticFilter = null,
        $aAddFilters  = array(),
        $aGroup = array()
    ) {
        $filters['host_register'] = '1';
        if (is_array($filterType)) {
            $filterType['host_register'] = 'AND';
        } else {
            $filterType = array(
                '*' => $filterType,
                'host_register' => 'AND'
            );
        }
                        
        return parent::getList($parameterNames, $count, $offset, $order, $sort, $filters, $filterType, null, null, $aAddFilters, $aGroup);
    }
    
    /**
     * 
     * @param type $parameterNames
     * @param type $count
     * @param type $offset
     * @param type $order
     * @param type $sort
     * @param array $filters
     * @param type $filterType
     * @return type
     */
    public static function getListBySearch(
        $parameterNames = "*",
        $count = -1,
        $offset = 0,
        $order = null,
        $sort = "ASC",
        $filters = array(),
        $filterType = "OR"
    ) {
        $aAddFilters = array();
        $tablesString =  '';
        $aGroup = array();
                 
        $filters['host_register'] = '1';
        if (is_array($filterType)) {
            $filterType['host_register'] = 'AND';
        } else {
            $filterType = array(
                '*' => $filterType,
                'host_register' => 'AND'
            );
        }
                
        if (array('tagname', array_values($filters)) && !empty($filters['tagname'])) {
            $aAddFilters = array(
                'tables' => array('cfg_tags', 'cfg_tags_hosts'),
                'join'   => array(
                    'cfg_tags.tag_id = cfg_tags_hosts.tag_id', 
                    'cfg_tags_hosts.resource_id = cfg_hosts.host_id '
                )
            ); 
        }
        
        if (isset($filters['tagname']) && count($filters['tagname']) > 1) {
            $aGroup = array('sField' => 'cfg_tags_hosts.resource_id', 'nb' => count($filters['tagname']));
        }
               
        return parent::getListBySearch($parameterNames, $count, $offset, $order, $sort, $filters, $filterType, $tablesString, null, $aAddFilters, $aGroup);
    }

    /**
     * Used for duplicate a host
     *
     * @param int $sourceObjectId The source host id
     * @param int $duplicateEntries The number entries
     * @return array List of new host id
     */
    public static function duplicate($sourceObjectId, $duplicateEntries = 1)
    {
        $db = Di::getDefault()->get(static::$databaseName);
        $sourceParams = static::getParameters($sourceObjectId, '*');
        if (false === $sourceParams) {
            throw new \Exception(static::OBJ_NOT_EXIST);
        }
        unset($sourceParams['host_id']);
        $originalName = $sourceParams['host_name'];
        $explodeOriginalName = explode('_', $originalName);
        $j = 0;
        if (($count = count($explodeOriginalName)) > 1 && is_numeric($explodeOriginalName[$count - 1])) {
            $originalName = join('_', array_slice($explodeOriginalName, 0, -1));
            $j = $explodeOriginalName[$count - 1];
        }

        $listDuplicateId = array();
        for ($i = 0; $i < $duplicateEntries; $i++) {
            /* Search the unique name for duplicate host */
            do {
                $j++;
                $unique = self::isUnique($originalName . '_' . $j);
            } while (false === $unique);
            $sourceParams['host_name'] = $originalName . '_' . $j;
            /* Insert the duplicate host */
            $lastId = static::insert($sourceParams);
            if (false === is_numeric($lastId)) {
                throw new \Exception("The value is not numeric");
            }
            $listDuplicateId[] = $lastId;
            /* Insert relation */
            /* Duplicate service */
            /*   Get service for the source host */
            $listSvc = HostServiceRelation::getTargetIdFromSourceId('service_service_id', 'host_host_id', $sourceObjectId);
            foreach ($listSvc as $svcId) {
                /* Duplicate service */
                $newSvcId = Service::duplicate($svcId, 1, true);
                if (count($newSvcId) > 0) {
                    /* Attach the new service to the new host */
                    HostServiceRelation::insert($lastId, $newSvcId[0]);
                }
            }
            $db->beginTransaction();
            /* Duplicate macros */
            $queryDupMacros = "INSERT INTO cfg_customvariables_hosts (host_macro_name, host_macro_value, is_password, host_host_id)
                SELECT host_macro_name, host_macro_value, is_password, " . $lastId . " FROM cfg_customvariables_hosts
                    WHERE host_host_id = :sourceObjectId";
            $stmt = $db->prepare($queryDupMacros);
            $stmt->bindParam(':sourceObjectId', $sourceObjectId);
            $stmt->execute();
            /* Host template */
            $queryDupTemplate = "INSERT INTO cfg_hosts_templates_relations (host_host_id, host_tpl_id, `order`)
                SELECT " . $lastId . ", host_tpl_id, `order` FROM cfg_hosts_templates_relations
                    WHERE host_host_id = :sourceObjectId";
            $stmt = $db->prepare($queryDupTemplate);
            $stmt->bindParam(':sourceObjectId', $sourceObjectId);
            $stmt->execute();
            /* Host global tags */
            $queryDupTag = "INSERT INTO cfg_tags_hosts (tag_id, resource_id)
                SELECT th.tag_id, " . $lastId . " FROM cfg_tags_hosts th, cfg_tags t
                    WHERE t.user_id IS NULL AND t.tag_id = th.tag_id AND th.resource_id = :sourceObjectId";
            $stmt = $db->prepare($queryDupTag);
            $stmt->bindParam(':sourceObjectId', $sourceObjectId);
            $stmt->execute();
            $db->commit();
        }
    }
    
    /**
     * @param string $paramName
     * @param array $paramValues
     * @param array $extraConditions
     * @return array
     */
    public static function getIdByParameter($paramName, $paramValues = array(), $extraConditions = array())
    {        
        $extraConditions['host_register'] = '1';
        return parent::getIdByParameter($paramName, $paramValues, $extraConditions);
    }
}
