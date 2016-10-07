--
-- Table structure for table `waa_authorization`
--

CREATE TABLE `waa_authorization` (
`authid` int(11) NOT NULL,
  `operatorid` int(11) NOT NULL,
  `deviceid` int(11) NOT NULL,
  `clientid` varchar(256) DEFAULT NULL,
  `dtmcreated` int(11) NOT NULL DEFAULT '0',
  `dtmmodified` int(11) NOT NULL DEFAULT '0',
  `accesstoken` varchar(256) DEFAULT NULL,
  `dtmaccesscreated` int(11) NOT NULL,
  `dtmaccessexpires` int(11) NOT NULL,
  `refreshtoken` varchar(256) DEFAULT NULL,
  `dtmrefreshcreated` int(11) NOT NULL,
  `dtmrefreshexpires` int(11) NOT NULL,
  `previousaccesstoken` varchar(256) DEFAULT NULL,
  `previousrefreshtoken` varchar(256) DEFAULT NULL
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `waa_device`
--

CREATE TABLE `waa_device` (
`id` int(11) NOT NULL,
  `deviceuuid` varchar(1024) NOT NULL,
  `platform` varchar(64) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `os` varchar(64) DEFAULT NULL,
  `osversion` varchar(32) DEFAULT NULL,
  `dtmcreated` int(11) NOT NULL DEFAULT '0',
  `dtmmodified` int(11) NOT NULL DEFAULT '0'
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wci_chat_extension`
--

CREATE TABLE `wci_chat_extension` (
`id` int(11) NOT NULL,
  `chatid` int(11) NOT NULL,
  `revision` int(11) NOT NULL
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wci_revision`
--

CREATE TABLE `wci_revision` (
  `id` int(11) NOT NULL
) DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `waa_authorization`
--
ALTER TABLE `waa_authorization`
 ADD PRIMARY KEY (`authid`), ADD KEY `idx_accesstoken` (`accesstoken`(255)), ADD KEY `idx_refreshtoken` (`refreshtoken`(255)), ADD KEY `idx_deviceid` (`deviceid`), ADD KEY `idx_previousaccesstoken` (`previousaccesstoken`(255));

--
-- Indexes for table `waa_device`
--
ALTER TABLE `waa_device`
 ADD PRIMARY KEY (`id`), ADD KEY `idx_device` (`deviceuuid`(255),`platform`);

--
-- Indexes for table `wci_chat_extension`
--
ALTER TABLE `wci_chat_extension`
 ADD PRIMARY KEY (`id`), ADD KEY `chatid` (`chatid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `waa_authorization`
--
ALTER TABLE `waa_authorization`
MODIFY `authid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `waa_device`
--
ALTER TABLE `waa_device`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `wci_chat_extension`
--
ALTER TABLE `wci_chat_extension`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `wci_chat_extension`
--
-- This fails on some systems
-- ALTER TABLE `wci_chat_extension`
-- ADD CONSTRAINT `wci_chat_extension_ibfk_1` FOREIGN KEY (`chatid`) REFERENCES `lh_chat` (`id`) ON DELETE CASCADE;


--
-- Initialize table `wci_revision`
--
INSERT INTO `wci_revision` VALUES (1);


