<?php
/**
 * JArticle_Class
 *
 * @version  1.0
 * @package Stilero
 * @subpackage JArticle_Class
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2012-nov-29 Stilero Webdesign (www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class K2JArticleUrl15 extends JArticleUrl15{
    
    public function __construct($JArticle) {
        parent::__construct($JArticle);
    }
    
    protected function buildQuery($select, $from, $whereCol, $whereVal){
        $db = JFactory::getDbo();
        $query = 'SELECT '.$select.
                ' FROM '.$db->nameQuote($from).
                ' WHERE '.$whereCol.' = '.$db->Quote($whereVal);
        return $query;
    }
    
    protected function isExtensionInstalled($option){
        $db = JFactory::getDbo();
        $query = $this->buildQuery('*', '#__components', 'option', $option);
        $db->setQuery($query);
        $result = $db->loadObject();
        if(!$result){
            return FALSE;
        }
        return TRUE;
    }
    
    protected function _categoryAlias(){
        $alias = '';
        if(isset($this->article->category->alias)){
            $alias = $this->article->category->alias;
        }
        return $alias;
    }
    
    protected function _articleAlias(){
        $alias = '';
        if(isset($this->article->alias)){
            $alias = $this->article->alias;
        }
        return $alias;
    }
    
    protected function _articleSlug(){
        return parent::_articleSlug();
    }
    
    protected function _initSh404SefUrls(){
        parent::_initSh404SefUrls();
    }
    
    protected function _attachSh404SefRouting(){
        parent::_attachSh404SefRouting();
        
    }
    
    protected function _joomlaSefUrlFromRoute(){
        return parent::_joomlaSefUrlFromRoute();
    }
        
    public function url(){
        $indexStart = strpos($this->article->link, 'index.php');
        $url = substr($this->article->link, $indexStart);
        $absUrl = JURI::Root().$url;
        return $absUrl;
        //return $this->_joomlaSefUrlFromRoute();
    }
}
