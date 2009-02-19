# TYPO3 Extension Manager dump 1.1
#
# Host: localhost    Database: typo3_wapplersystems
#--------------------------------------------------------


#
# Table structure for table "tx_wsstats_tracking"
#
CREATE TABLE tx_wsstats_tracking (
  id int(11) NOT NULL auto_increment,
  wsstats_id varchar(50) default '',
  timestamp varchar(20) default '',
  ip varchar(35) default '',
  hostname varchar(255) default '',
  urlrequested text,
  agent varchar(255) default '',
  referrer text,
  searchphrase varchar(255) default '',
  searchpage int(11) default '0',
  os varchar(15) default '',
  browser varchar(50) default '',
  language varchar(5) default '',
  searchengine varchar(20) default '',
  bot int(1) default '0',
  feed varchar(30) default '',
  fe_user varchar(50) default '',
  cookiekey varchar(255) default '',
  UNIQUE id (id),
  KEY wsstats_id (wsstats_id),
  KEY timestamp (timestamp)
);