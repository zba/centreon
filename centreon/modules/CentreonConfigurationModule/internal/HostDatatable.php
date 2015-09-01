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

namespace CentreonConfiguration\Internal;

use Centreon\Internal\Datatable\Datasource\CentreonDb;
use CentreonMain\Events\SlideMenu;
use Centreon\Internal\Di;
use Centreon\Internal\Utils\HumanReadable;
use CentreonRealtime\Repository\HostRepository as RealTimeHostRepository;
use CentreonConfiguration\Repository\HostRepository;
use CentreonConfiguration\Repository\HostTemplateRepository;
use CentreonAdministration\Repository\TagsRepository;
use Centreon\Internal\Datatable;
use CentreonRealtime\Repository\ServiceRepository as ServiceRealTimeRepository;
use CentreonConfiguration\Models\Poller;

/**
 * Description of HostDatatable
 *
 * @author lionel
 */
class HostDatatable extends Datatable
{
    /**
     *
     * @var type 
     */
    protected static $objectId = 'host_id';

    /**
     *
     * @var type 
     */
    protected static $dataprovider = '\Centreon\Internal\Datatable\Dataprovider\CentreonDb';
    
    /**
     *
     * @var type 
     */
    protected static $datasource = '\CentreonConfiguration\Models\Host';
    
    /**
     *
     * @var array 
     */
    
    /**
     *
     * @var type 
     */
    protected static $rowIdColumn = array('id' => 'host_id', 'name' => 'host_name');
    
    /**
     *
     * @var array 
     */
    protected static  $aFieldNotAuthorized = array('tagname');

    /**
     *
     * @var array 
     */
    public static $configuration = array(
        'autowidth' => false,
        'order' => array(
            array('host_name', 'asc')
        ),
        'stateSave' => false,
        'paging' => true
    );
    
    /**
     *
     * @var array 
     */
    public static $columns = array(
        array (
            'title' => "Id",
            'name' => 'host_id',
            'data' => 'host_id',
            'orderable' => false,
            'searchable' => false,
            'type' => 'string',
            'visible' => false,
            'width' => '20px',
            'className' => "cell_center"
        ),
        array (
            'title' => 'Host',
            'name' => 'host_name',
            'data' => 'host_name',
            'orderable' => true,
            'searchable' => true,
            'searchLabel' => 'host',
            'type' => 'string',
            'visible' => true,
            'cast' => array(
                'type' => 'url',
                'parameters' => array(
                    'route' => '/centreon-configuration/host/[i:id]',
                    'routeParams' => array(
                        'id' => '::host_id::'
                    ),
                    'linkName' => '::host_name::'
                )
            ),
            'searchParam' => array(
                'main' => 'true',
            )
        ),
        array (
            'title' => 'Description',
            'name' => 'host_alias',
            'data' => 'host_alias',
            'orderable' => true,
            'searchable' => true,
            'type' => 'string',
            'visible' => true,
        ),
        array (
            'title' => 'IP Address / DNS',
            'name' => 'host_address',
            'data' => 'host_address',
            'orderable' => false,
            'searchable' => true,
            'type' => 'string',
            'visible' => true,
            'className' => "cell_center"
        ),
        array (
            'title' => 'Poller',
            'name' => 'poller_id',
            'data' => 'poller_id',
            'orderable' => true,
            'searchable' => false,
            'searchLabel' => 'poller',
            'type' => 'string',
            'visible' => true,
        ),
        array (
            'title' => 'Interval',
            'name' => 'host_check_interval',
            'data' => 'host_check_interval',
            'orderable' => false,
            'searchable' => false,
            'type' => 'string',
            'visible' => false,
            'className' => "cell_center"
        ),
        array (
            'title' => 'Retry',
            'name' => 'host_retry_check_interval',
            'data' => 'host_retry_check_interval',
            'orderable' => false,
            'searchable' => false,
            'type' => 'string',
            'visible' => false,
            'className' => "cell_center"
        ),
        array (
            'title' => 'Attempts',
            'name' => 'host_max_check_attempts',
            'data' => 'host_max_check_attempts',
            'orderable' => false,
            'searchable' => false,
            'type' => 'string',
            'visible' => false,
            'className' => "cell_center"
        ),
        array (
            'title' => 'Templates',
            'name' => 'host_id as host_template',
            'data' => 'host_template',
            'orderable' => false,
            'searchable' => false,
            'type' => 'string',
            'visible' => true,
            'className' => "cell_center",
            'width' => "20px"
        ),
        array (
            'title' => 'Status',
            'name' => 'host_activate',
            'data' => 'host_activate',
            'orderable' => true,
            'searchable' => true,
            'type' => 'string',
            'visible' => true,
            'cast' => array(
                'type' => 'select',
                'parameters' => array(
                    '0' => '<span class="label label-danger">Disabled</span>',
                    '1' => '<span class="label label-success">Enabled</span>',
                    '2' => 'Trash',
                )
            ),
            'searchParam' => array(
                'main' => 'true',
                'type' => 'select',
                'additionnalParams' => array(
                    'Enabled' => '1',
                    'Disabled' => '0'
                )
            ),
            'className' => "cell_center"
        ), 
        array (
            'title' => 'Tags',
            'name' => 'tagname',
            'data' => 'tagname',
            'orderable' => false,
            'searchable' => true,
            'type' => 'string',
            'visible' => true,
            'tablename' => 'cfg_tags'
        )
    );
    
    protected static $extraParams = array(
        'addToHook' => array(
            'objectType' => 'host'
        )
    );

    protected static $hookParams = array(
        'resourceType' => 'host'
    );
    
    /**
     * 
     * @param array $params
     */
    public function __construct($params, $objectModelClass = '')
    {
        parent::__construct($params, $objectModelClass);
    }
    
    /**
     * 
     * @param array $resultSet
     */
    protected function formatDatas(&$resultSet)
    {
        $router = Di::getDefault()->get('router');
        
        foreach ($resultSet as &$myHostSet) {

            $myHostSet['host_name'] ='<span class="icoListing">'.HostRepository::getIconImage($myHostSet['host_name']).'</span>'.
                $myHostSet['host_name'];

            $sideMenuCustom = new SlideMenu($myHostSet['host_id']);
            
            $events = Di::getDefault()->get('events');
            $events->emit('centreon-configuration.slide.menu.host', array($sideMenuCustom));
            
            $myHostSet['DT_RowData']['right_side_menu_list'] = $sideMenuCustom->getMenu();
            $myHostSet['DT_RowData']['right_side_default_menu'] = $sideMenuCustom->getDefaultMenu();

            /* Host State */
            $myHostSet['host_name'] .= RealTimeHostRepository::getStatusBadge(
                RealTimeHostRepository::getStatus($myHostSet['host_id'])
            );

            /* Poller */
            if (isset($myHostSet["poller_id"]) && $myHostSet["poller_id"] != "") {
                $poller = Poller::getParameters($myHostSet["poller_id"], 'name');
                $myHostSet["poller_id"] = $poller['name'];
            } else {
                $myHostSet["poller_id"] = "";
            }

            /* Templates */
            $myHostSet['host_template']  = "";

            $templates = HostRepository::getTemplateChain($myHostSet['host_id'], array(), 1);
            foreach ($templates as $template) {
                $myHostSet['host_template'] .= '<a href="'
                . $router->getPathFor("/centreon-configuration/hosttemplate/[i:id]", array('id' => $template['id']))
                . '"><i class="icon-template ico-20"></i></a>';
            }

            /* Display human readable the check/retry interval */
            $myHostSet['host_check_interval'] = HumanReadable::convert($myHostSet['host_check_interval'], 's', $units, null, true);
            $myHostSet['host_retry_check_interval'] = HumanReadable::convert($myHostSet['host_retry_check_interval'], 's', $units, null, true);

            /* Get personal tags */
            $myHostSet['tagname'] = '';
            $aTagUsed = array();

            $aTags = TagsRepository::getList('host', $myHostSet['host_id'], 0, 0);

            foreach ($aTags as $oTags) {
                if (!in_array($oTags['id'], $aTagUsed)) {
                    $aTagUsed[] = $oTags['id'];
                    $myHostSet['tagname'] .= TagsRepository::getTag('host',$myHostSet['host_id'], $oTags['id'], $oTags['text'], $oTags['user_id'], 1);
                }
            }

            $myHostSet['tagname'] .= TagsRepository::getAddTag('host', $myHostSet['host_id']);
        }
    }
    
}
