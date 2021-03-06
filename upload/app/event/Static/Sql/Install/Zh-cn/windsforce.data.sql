-- WINDSFORCE 活动数据库数据
-- version 1.1.1
-- http://www.windsforce.com
--
-- 开发: Windsforce TEAM
-- 网站: http://www.windsforce.com

--
-- 数据库: Event初始化数据
--

-- --------------------------------------------------------

--
-- 转存表中的数据 `windsforce_eventoption`
--

INSERT INTO `{WINDSFORCE}eventoption` (`eventoption_name`, `eventoption_value`) VALUES
('event_close', '0'),
('front_add', '1'),
('event_audit', '0');

-- --------------------------------------------------------

--
-- 转存表中的数据 `windsforce_eventcategory`
--

INSERT INTO `{WINDSFORCE}eventcategory` (`eventcategory_id`, `eventcategory_name`, `eventcategory_parentid`, `create_dateline`, `update_dateline`, `eventcategory_count`, `eventcategory_sort`) VALUES
(1, '音乐', 0, 1372414489, 0, 0, 0),
(2, '戏剧', 0, 1372414504, 1372414517, 0, 0),
(3, '聚会', 0, 1372414535, 0, 0, 0),
(4, '电影', 0, 1372414546, 0, 0, 0),
(5, '讲座', 7, 1372414559, 1372415770, 0, 0),
(6, '展览', 7, 1372414573, 1372415787, 0, 0),
(7, '其他', 0, 1372414583, 0, 0, 0),
(8, '小型现场', 1, 1372414615, 0, 0, 0),
(9, '音乐会', 1, 1372414636, 0, 0, 0),
(10, '演唱会', 1, 1372414654, 0, 0, 0),
(11, '音乐节', 1, 1372414669, 0, 0, 0),
(12, '话剧', 2, 1372414684, 0, 0, 0),
(13, '音乐剧', 2, 1372414725, 0, 0, 0),
(14, '舞剧', 2, 1372414927, 0, 0, 0),
(15, '歌剧', 2, 1372414938, 0, 0, 0),
(16, '戏曲', 2, 1372414957, 0, 0, 0),
(17, '生活', 3, 1372415341, 1374866415, 0, 0),
(18, '摄影', 3, 1372415361, 0, 0, 0),
(19, '外语', 3, 1372415409, 0, 0, 0),
(20, '桌游', 3, 1372415427, 1372415819, 0, 0),
(21, '交友', 3, 1372415443, 0, 0, 0),
(22, '集市', 3, 1372415592, 0, 0, 0),
(23, '主题放映', 4, 1372415635, 0, 0, 0),
(24, '影展', 4, 1372415650, 0, 0, 0),
(25, '影院活动', 4, 1372415667, 0, 0, 0),
(26, '运动', 7, 1372415874, 0, 0, 0),
(27, '旅行', 7, 1372415884, 0, 0, 0),
(28, '公益', 7, 1372415906, 0, 0, 0);
