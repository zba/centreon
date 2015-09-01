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

namespace Centreon\Internal\Form\Validators;

/**
 * @author Maximilien Bersoult <mbersoult@centreon.com>
 *
 * @package Centreon
 * @subpackage Core
 */
class IllegalChars extends ForbiddenChar
{
    /**
     * Validate function for illegal characters
     *
     * Use the object illegal characters constant
     *
     * Validator params
     *   - None
     *
     * @param string $value The value to validate
     * @param array $params The list of parameters for this validator
     * @return array
     */
    public function validate($value, $params = array()) {
        $params['characters'] = CENTREON_ILLEGAL_CHAR_OBJ;
        return parent::validate($value, $params);
    }
}

