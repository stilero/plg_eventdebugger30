<?php
/**
 * Share Logger Class
 * Handles logs to the DB
 *
 * @version  1.0
 * @package Stilero
 * @subpackage Sharechecker_Class
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2012-dec-04 Stilero Webdesign (www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class ShareLogger30 extends ShareLogger{
    
    public function __construct($JArticle, $table, $plgLangPrefix) {
        parent::__construct($JArticle, $table, $plgLangPrefix);
    }
    
    /**
     * Check if the log-table already exists
     * @return boolean 
     * @since 3.0
     */
    public function isTableExisting() {
        $db =& JFactory::getDbo();
        $query = 'DESC '.$db->quoteName($this->_table);
        $db->setQuery($query);
        $isTableFound = $db->query();
        var_dump($isTableFound);exit;
        if($isTableFound){
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Create the table from scratch
     * @return boolean false on fail, true on success
     * @since 3.0
     */
    public function createTable() {
        $db =& JFactory::getDbo();
        $queryDrop = 'DROP TABLE IF EXISTS '.$db->quoteName($this->_table);
        $queryCreate = "CREATE TABLE ".$db->quoteName($this->_table)." (
                `id` int(11) NOT NULL auto_increment,
                `article_id` int(11) NOT NULL default 0,
                `cat_id` int(11) NOT NULL default 0,
                `articlelink` varchar(255) NOT NULL default '',
                `date` datetime NOT NULL default '0000-00-00 00:00:00',
                `language` char(7) NOT NULL default '',
                PRIMARY KEY  (`id`)
                ) DEFAULT CHARSET=utf8;";
        $db->setQuery($queryDrop);
        $resultDrop = $db->query();
        if(!$resultDrop){
            $this->setError(self::$ERROR_TYPE_ERROR, $this->_plgLangPrefix.'DROP_TABLE_FAILED', $queryDrop);
            return false;
        }
        $db->setQuery($queryCreate);
        $resultCreate = $db->query();
        if($resultCreate){
            return TRUE;
        }
        $this->setError(self::$ERROR_TYPE_ERROR, $this->_plgLangPrefix.'CREATE_TABLE_FAILED', $queryCreate);
        return FALSE;
    }
    
    /**
     * Save to log
     * @return boolean true on success
     * @since 3.0
     */
    public function log() {
        $itemId = $this->_article->id;
        $catId = $this->_article->catid;
        $option = JRequest::getCmd('option');
        $now = date("Y-m-d H:i:s");
        $language = $this->_article->language;
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->insert($db->quoteName($this->_table));
        $query->set('article_id = '.$db->quote((int)$itemId));
        $query->set('cat_id = '.$db->quote((int)$catId));
        $query->set('articlelink = '.$db->quote($option));
        $query->set('date = '.$db->quote($now));
        $query->set('language = '.$db->quote($language));
        $db->setQuery($query);
        $result = $db->query($query);
        if($result){
            return true;
        }
        $this->setError(self::$ERROR_TYPE_ERROR, $this->_plgLangPrefix.'SAVE_LOG_FAILED', $query->dump());
        return FALSE;
    }
    
    /**
     * Delete log from table
     * @return boolean true on success
     * @since 3.0
     */
    public function unlog() {
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete($db->quoteName($this->_table));
        $query->where('article_id = '.$db->quote((int)$this->_article->id));
        $query->where('articlelink = '.$db->quote(JRequest::getVar('option')));
        $db->setQuery($query);
        $result = $db->query($query);
        if($result){
            return true;
        }
        $this->setError(self::$ERROR_TYPE_ERROR, $this->_plgLangPrefix.'UNLOG_FAILED', $query->dump());
        return FALSE;
    }
    
    /**
     * Check if a log aldready exists for the actual article
     * @return boolean true on found
     * @since 3.0
     */
    public function isLogged(){
        $db =& JFactory::getDbo();
        $query = $db->getQuery(TRUE);
        $query->select('id');
        $query->from($db->quoteName($this->_table));
        $query->where('article_id = '.$db->quote((int)$this->_article->id));
        $query->where('articlelink = '.$db->quote(JRequest::getVar('option')));
        $db->setQuery($query);
        $isLogged = $db->loadObject();
        if($isLogged != NULL){
            $this->setError(self::$ERROR_TYPE_NOTICE, $this->_plgLangPrefix.'ALREADYSENT', $query->dump());
            return TRUE;
        }
        return FALSE;
    }
}
