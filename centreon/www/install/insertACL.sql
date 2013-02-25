
INSERT INTO `acl_groups` (`acl_group_id`, `acl_group_name`, `acl_group_alias`, `acl_group_changed`, `acl_group_activate`) VALUES (1, 'ALL', 'ALL', 0, '1');
INSERT INTO `acl_group_contactgroups_relations` (`agcgr_id`, `cg_cg_id`, `acl_group_id`) VALUES (1, 3, 1);

INSERT INTO `acl_actions` (`acl_action_id`, `acl_action_name`, `acl_action_description`, `acl_action_activate`) VALUES (1, 'Simple User', 'Simple User', '1');
INSERT INTO `acl_actions_rules` (`aar_id`, `acl_action_rule_id`, `acl_action_name`) VALUES (1, 1, 'poller_stats'), (2, 1, 'top_counter'), (3, 1, 'service_acknowledgement'), (4, 1, 'service_schedule_check'), (5, 1, 'service_schedule_forced_check'), (6, 1, 'service_schedule_downtime'), (7, 1, 'service_comment'), (8, 1, 'host_acknowledgement'), (9, 1, 'host_schedule_check'), (10, 1, 'host_schedule_forced_check'), (11, 1, 'host_schedule_downtime'), (12, 1, 'host_comment');
INSERT INTO `acl_group_actions_relations` (`agar_id`, `acl_action_id`, `acl_group_id`) VALUES (1, 1, 1);

INSERT INTO `acl_resources` (`acl_res_id`, `acl_res_name`, `acl_res_alias`, `all_hosts`, `all_hostgroups`, `all_servicegroups`, `acl_res_activate`, `acl_res_comment`, `acl_res_status`, `changed`) VALUES (1, 'All Resources', 'All Resources', '1', '1', '1', '1', NULL, NULL, 0);
INSERT INTO `acl_res_group_relations` (`argr_id`, `acl_res_id`, `acl_group_id`) VALUES (1, 1, 1);

INSERT INTO `acl_topology` (`acl_topo_id`, `acl_topo_name`, `acl_topo_alias`, `acl_topo_activate`) VALUES (1, 'Configuration', 'Configuration', '1'), (2, 'Monitoring + Home', 'Monitoring + Home', '1'), (3, 'Reporting', 'Reporting', '1'), (4, 'Graphs', 'Graphs', '1');
INSERT INTO `acl_topology_relations` (`agt_id`, `topology_topology_id`, `acl_topo_id`, `access_right`) VALUES
(1, 6, 1, 1),
(2, 79, 1, 1),
(4, 84, 1, 2),
(5, 85, 1, 2),
(7, 80, 1, 1),
(8, 87, 1, 2),
(9, 88, 1, 2),
(10, 89, 1, 2),
(11, 90, 1, 2),
(12, 160, 1, 2),
(13, 92, 1, 2),
(14, 94, 1, 2),
(15, 95, 1, 2),
(16, 96, 1, 1),
(18, 97, 1, 2),
(19, 98, 1, 2),
(20, 99, 1, 2),
(21, 100, 1, 2),
(22, 106, 1, 1),
(23, 107, 1, 1),
(24, 108, 1, 1),
(25, 109, 1, 1),
(26, 110, 1, 1),
(27, 169, 1, 1),
(28, 171, 1, 1),
(29, 172, 1, 1),
(30, 173, 1, 1),
(31, 174, 1, 1),
(32, 176, 1, 1),
(33, 175, 1, 1),
(34, 178, 1, 1),
(35, 179, 1, 1),
(36, 180, 1, 1),
(37, 181, 1, 1),
(38, 182, 1, 1),
(39, 168, 1, 0),
(40, 103, 1, 0),
(41, 105, 1, 0),
(42, 1, 2, 1),
(45, 185, 2, 1),
(46, 186, 2, 1),
(49, 2, 2, 1),
(50, 22, 2, 1),
(51, 24, 2, 1),
(52, 25, 2, 1),
(53, 26, 2, 1),
(54, 27, 2, 1),
(55, 28, 2, 1),
(56, 29, 2, 1),
(57, 30, 2, 1),
(58, 31, 2, 1),
(59, 23, 2, 1),
(60, 41, 2, 1),
(61, 42, 2, 1),
(62, 43, 2, 1),
(63, 44, 2, 1),
(64, 32, 2, 1),
(65, 52, 2, 1),
(66, 53, 2, 1),
(67, 54, 2, 1),
(68, 33, 2, 1),
(69, 55, 2, 1),
(70, 56, 2, 1),
(71, 57, 2, 1),
(72, 45, 2, 1),
(73, 58, 2, 1),
(74, 59, 2, 1),
(75, 60, 2, 1),
(76, 46, 2, 1),
(77, 61, 2, 1),
(78, 62, 2, 1),
(80, 47, 2, 1),
(81, 64, 2, 1),
(82, 65, 2, 1),
(83, 66, 2, 1),
(84, 48, 2, 1),
(85, 67, 2, 1),
(86, 68, 2, 1),
(87, 69, 2, 1),
(88, 40, 2, 1),
(89, 36, 2, 1),
(90, 34, 2, 1),
(91, 35, 2, 1),
(92, 17, 2, 1),
(93, 21, 2, 1),
(94, 20, 2, 1),
(95, 18, 2, 1),
(96, 19, 2, 1),
(97, 38, 2, 1),
(98, 39, 2, 1),
(99, 70, 2, 1),
(100, 187, 2, 1),
(102, 84, 2, 0),
(103, 85, 2, 0),
(104, 87, 2, 0),
(105, 88, 2, 0),
(106, 89, 2, 0),
(107, 90, 2, 0),
(108, 160, 2, 0),
(109, 92, 2, 0),
(110, 94, 2, 0),
(111, 95, 2, 0),
(112, 97, 2, 0),
(113, 98, 2, 0),
(114, 99, 2, 0),
(115, 100, 2, 0),
(116, 171, 2, 0),
(117, 172, 2, 0),
(118, 173, 2, 0),
(119, 174, 2, 0),
(120, 176, 2, 0),
(121, 175, 2, 0),
(122, 178, 2, 0),
(123, 179, 2, 0),
(124, 180, 2, 0),
(125, 181, 2, 0),
(126, 182, 2, 0),
(127, 168, 2, 0),
(128, 103, 2, 0),
(129, 105, 2, 0),
(130, 3, 3, 1),
(131, 145, 3, 1),
(132, 146, 3, 1),
(133, 148, 3, 1),
(134, 149, 3, 1),
(136, 84, 3, 0),
(137, 85, 3, 0),
(138, 87, 3, 0),
(139, 88, 3, 0),
(140, 89, 3, 0),
(141, 90, 3, 0),
(142, 160, 3, 0),
(143, 92, 3, 0),
(144, 94, 3, 0),
(145, 95, 3, 0),
(146, 97, 3, 0),
(147, 98, 3, 0),
(148, 99, 3, 0),
(149, 100, 3, 0),
(150, 171, 3, 0),
(151, 172, 3, 0),
(152, 173, 3, 0),
(153, 174, 3, 0),
(154, 176, 3, 0),
(155, 175, 3, 0),
(156, 178, 3, 0),
(157, 179, 3, 0),
(158, 180, 3, 0),
(159, 181, 3, 0),
(160, 182, 3, 0),
(161, 168, 3, 0),
(162, 103, 3, 0),
(163, 105, 3, 0),
(164, 4, 4, 1),
(165, 150, 4, 1),
(166, 152, 4, 1),
(167, 154, 4, 1),
(168, 155, 4, 1),
(170, 84, 4, 0),
(171, 85, 4, 0),
(172, 87, 4, 0),
(173, 88, 4, 0),
(174, 89, 4, 0),
(175, 90, 4, 0),
(176, 160, 4, 0),
(177, 92, 4, 0),
(178, 94, 4, 0),
(179, 95, 4, 0),
(180, 97, 4, 0),
(181, 98, 4, 0),
(182, 99, 4, 0),
(183, 100, 4, 0),
(184, 171, 4, 0),
(185, 172, 4, 0),
(186, 173, 4, 0),
(187, 174, 4, 0),
(188, 176, 4, 0),
(189, 175, 4, 0),
(190, 178, 4, 0),
(191, 179, 4, 0),
(192, 180, 4, 0),
(193, 181, 4, 0),
(194, 182, 4, 0),
(195, 168, 4, 0),
(196, 103, 4, 0),
(197, 105, 4, 0);
INSERT INTO `acl_group_topology_relations` (`agt_id`, `acl_group_id`, `acl_topology_id`) VALUES (1, 1, 1), (2, 1, 2), (3, 1, 3), (4, 1, 4);


