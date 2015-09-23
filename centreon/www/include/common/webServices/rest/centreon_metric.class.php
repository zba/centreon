<?php
/*
 * Copyright 2005-2015 Centreon
 * Centreon is developped by : Julien Mathis and Romain Le Merlus under
 * GPL Licence 2.0.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation ; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses>.
 *
 * Linking this program statically or dynamically with other modules is making a
 * combined work based on this program. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 *
 * As a special exception, the copyright holders of this program give Centreon
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of Centreon choice, provided that
 * Centreon also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 *
 * SVN : $URL$
 * SVN : $Id$
 *
 */

global $centreon_path;
require_once $centreon_path . "/www/class/centreonBroker.class.php";
require_once $centreon_path . "/www/class/centreonDB.class.php";
require_once $centreon_path . "/www/class/centreonGraphService.class.php";
require_once dirname(__FILE__) . "/webService.class.php";

class CentreonMetric extends CentreonWebService {
    protected $pearDBMonitoring;

    /**
     * Constructor
     *
     * @param CentreonDB $db
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $brk = new CentreonBroker($this->pearDB);
        if ($brk->getBroker() == 'broker') {
            $this->pearDBMonitoring = new CentreonDB('centstorage');
        } else {
            $this->pearDBMonitoring = new CentreonDB('ndo');
        }
    }

    /**
     * Get metric list
     *
     * @return array
     */
    public function getList()
    {
        if (false === isset($this->arguments['q'])) {
            $q = '';
        } else {
            $q = $this->arguments['q'];
        }
        $query = "SELECT DISTINCT(`metric_name`) COLLATE utf8_bin as \"metric_name\" FROM `metrics` WHERE metric_name LIKE '%$q%' ORDER BY `metric_name` COLLATE utf8_general_ci ";
        $DBRESULT = $this->pearDBMonitoring->query($query);
        $metrics = array();
        while ($row = $DBRESULT->fetchRow()) {
            $metrics[] = array(
                'id' => $row['metric_name'],
                'text' => $row['metric_name']
            );
        }

        return $metrics;
    }
    
    /**
     * Get metrics datas for a service
     *
     * @return array
     */
    public function getMetricsDataByService()
    {
        global $centreon;
        
        $userId = $centreon->user->user_id;
        $isAdmin = $centreon->user->admin;
        
        /* Get ACL if user is not admin */
        if (!$isAdmin) {
            $acl = new CentreonACL($userId, $isAdmin);
            $aclGroups = $acl->getAccessGroupsString();
        }
        
        /* Validate options */
        if (false === isset($this->arguments['start']) ||
            false === is_numeric($this->arguments['start']) ||
            false === isset($this->arguments['end']) ||
            false === is_numeric($this->arguments['end'])) {
            throw new RestBadRequestException("Bad parameters");
        }

        $start = $this->arguments['start'];
        $end = $this->arguments['end'];
        
        /* Get the numbers of points */
        $rows = 200;
        if (isset($this->arguments['rows'])) {
            if (false === is_numeric($this->arguments['rows'])) {
                throw new RestBadRequestException("Bad parameters");
            }
            $rows = $this->arguments['rows'];
        }
        if ($rows < 10) {
            throw new RestBadRequestException("The rows must be greater as 10");
        }
        
        if (false === isset($this->arguments['ids'])) {
            self::sendJson(array());
        }
        
        /* Get the list of service ID */
        $ids = explode(',', $this->arguments['ids']);
        $result = array();
        
        foreach ($ids as $id) {
            list($hostId, $serviceId) = explode('_', $id);
            if (false === is_numeric($hostId) ||
                false === is_numeric($serviceId)) {
                throw new RestBadRequestException("Bad parameters");
            }

            /* Check ACL is not admin */
            if (!$isAdmin) {
                $query = "SELECT service_id
                    FROM centreon_acl
                    WHERE host_id = " . $hostId . "
                        AND service_id = " . $serviceId . "
                        AND group_id IN (" . $aclGroups . ")";
                $res = $this->pearDBMonitoring->query($query);
                if (0 == $res->numRows()) {
                    throw new RestForbiddenException("Access denied");
                }
            }

            $data = array();

            /* Prepare graph */
            try {
                /* Get index data */
                $indexData = CentreonGraphService::getIndexId($hostId, $serviceId, $this->pearDBMonitoring);
                /* Create a virtual session for graph */
                if (false === is_null($this->token)) {
                    $sessionId = $this->token;
                    $this->pearDB->query("INSERT INTO session (session_id, user_id) VALUES ('" . $this->token . "', " . $centreon->user->user_id . ")");
                } else {
                    $sessionId = session_id();
                }
                $graph = new CentreonGraphService($indexData, $sessionId);
                if (false === is_null($this->token)) {
                    $this->pearDB->query("DELETE FROM session WHERE session_id = '" . $this->token . "'");
                }
            } catch (Exception $e) {
                if (false === is_null($this->token)) {
                    $this->pearDB->query("DELETE FROM session WHERE session_id = '" . $this->token . "'");
                }
                throw new RestNotFoundException("Graph not found");
            }
            $graph->setRRDOption("start", $start);
            $graph->setRRDOption("end", $end);
            $graph->initCurveList();
            $graph->createLegend();

            $serviceData = $graph->getData($rows);
            
            
            /* Replace NaN */
            for ($i = 0; $i < count($serviceData); $i++) {
                if (isset($serviceData[$i]['data'])) {
                    $times = array_keys($serviceData[$i]['data']);
                    $values = array_map(array($this, "convertNaN"),
                        array_values($serviceData[$i]['data'])
                    );
                }
                $serviceData[$i]['data'] = $values;
                $serviceData[$i]['label'] = $serviceData[$i]['legend'];
                unset($serviceData[$i]['legend']);
                $serviceData[$i]['type'] = $serviceData[$i]['graph_type'];
                unset($serviceData[$i]['graph_type']);
            }
            $result[] = array(
                'service_id' => $id,
                'data' => $serviceData,
                'times' => $times,
                'size' => $rows
            );
        }
        
        return $result;
    }
    
    
    /**
     * Function for test is a value is NaN
     *
     * @param mixed $element The element to test
     * @return mixed null if NaN else the element
     */
    protected function convertNaN($element)
    {
        if (strtoupper($element) == 'NAN') {
            return null;
        }
        return $element;
    }
}
?>
