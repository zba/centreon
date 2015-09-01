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
 */

namespace Centreon\Internal\Database;

use Centreon\Internal\Exception;
use Centreon\Internal\Di;

/**
 * Description of Migrate
 *
 * @author lionel
 */
class Migrate extends PropelMigration
{
    /**
     * 
     */
    
    public $module;
    /**
     * 
     * @param string $module
     * @param string $migrationClassPath
     */
    public function __construct($module = 'centreon', $migrationClassPath = null)
    {
        parent::__construct('centreon');
        $this->module = $module;
        if (!is_null($migrationClassPath)) {
            $this->setOutputDir($migrationClassPath);
        }
    }
    
    /**
     * 
     */
    public function down()
    {
        $this->runPhing('migration-down');
    }
    
    /**
     * 
     */
    public function up()
    {
        $this->runPhing('migration-up');
    }
    
    /**
     * 
     */
    public function migrate()
    {
        InputOutput::display(_("Executes all migrations"));
        $migrationManager = new Manager($module, 'production');
        $cmd = $this->getPhinxCallLine() .'migrate ';
        $cmd .= '-c '. $migrationManager->getPhinxConfigurationFile();
        $cmd .= '-e '. $this->module;
        shell_exec($cmd);
    }
    
    /**
     * 
     */
    public function status()
    {
        $this->runPhing('status');
    }
}
