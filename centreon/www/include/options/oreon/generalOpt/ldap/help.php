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

$help = array();

/*
 * LDAP Informations
 */
$help['ar_name'] = dgettext('help', 'Name of configuration');
$help['ar_description'] = dgettext('help', 'Short description of configuration');
$help['ldap_auth_enable'] = dgettext('help', 'Enable LDAP authentification');
$help['ldap_store_password'] = dgettext('help', 'Whether or not the password should be stored in database');
$help['ldap_auto_import'] = dgettext('help', 'Can connect with LDAP without import');
$help['ldap_contact_tmpl'] = dgettext('help', 'The contact template for auto imported user.<br/>This template is applied for Monitoring Engine contact configuration and ACLs');
$help['ldap_srv_dns'] = dgettext('help', 'Use the DNS service for get LDAP host');
$help['ldap_srv_dns_ssl'] = dgettext('help', 'Enable SSL connection');
$help['ldap_srv_dns_tls'] = dgettext('help', 'Enable TLS connection');
$help['ldap_dns_use_domain'] = dgettext('help', 'Set the domain for search the service');
$help['ldap_search_limit'] = dgettext('help', 'Search size limit');
$help['ldap_search_timeout'] = dgettext('help', 'Search timeout');
$help['ldapConf'] = dgettext('help', 'Ldap server. Failover will take place if multiple servers are defined.<br/>If TLS is enabled, make sure to configure the certificate requirements on the ldap.conf file and restart your web server.');
$help['bind_dn'] = dgettext('help', 'User DN for connect to LDAP in read only');
$help['bind_pass'] = dgettext('help', 'Password for connect to LDAP in read only');
$help['protocol_version'] = dgettext('help', 'The version protocol for connect to LDAP<br/>Use version 3 for Active Directory');
$help['ldap_template'] = dgettext('help', 'Template for LDAP attribute');
$help['user_base_search'] = dgettext('help', 'The base DN for search users');
$help['group_base_search'] = dgettext('help', 'The base DN for search groups');
$help['user_filter'] = dgettext('help', 'The LDAP search filter for users<br/>Use %s in filter. The %s will replaced by login in autologin or * in LDAP import');
$help['group_filter'] = dgettext('help', 'The LDAP search filter for groups<br/>Use %s in filter. The %s will replaced by group name in autologin or * in contactgroup field');
$help['alias'] = dgettext('help', 'The login attribute<br/>In Centreon : Alias / Login');
$help['user_group'] = dgettext('help', 'The group attribute for user');
$help['user_name'] = dgettext('help', 'The user name<br/>In Centreon : Full Name');
$help['user_firstname'] = dgettext('help', 'The user firstname<br/>In Centreon : givenname');
$help['user_lastname'] = dgettext('help', 'The user lastname<br/>In Centreon : sn');
$help['user_email'] = dgettext('help', 'The user email<br/>In Centreon : Email');
$help['user_pager'] = dgettext('help', 'The user pager<br/>In Centreon : Pager');
$help['group_name'] = dgettext('help', 'The group name<br/>In Centreon : Contact Group Name');
$help['group_member'] = dgettext('help', 'The LDAP attribute for relation between group and user');