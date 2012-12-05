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

class K2JArticle15 extends JArticle15{
    
//    public $ACCESS_PUBLIC = '0';
//    public $ACCESS_REGISTRED = '1';
//    public $ACCESS_SPECIAL = '2';
//    public $STATE_PUBLISHED = '1';
//    public $STATE_UNPUBLISHED = '0';
    
    public function __construct($article) {
        parent::__construct($article);
    }
    
    protected function loadDependencies(){
        return;
    }
    
    protected function buildQuery($select, $from, $whereCol, $whereVal){
        $db = JFactory::getDbo();
        $query = 'SELECT '.$select.
                ' FROM '.$db->nameQuote($from).
                ' WHERE '.$whereCol.' = '.$db->Quote($whereVal);
        return $query;
    }
    
    protected function fetchCategoryTitle($catid){
        $db = JFactory::getDbo();
        $query = $this->buildQuery('name', '#__k2_categories', 'id', $catid);
        $db->setQuery($query);
        $result = $db->loadObject();
        if(!$result){
            return FALSE;
        }
        return $result->name;
    }
    
    public function categoryTitle($article){
        if(isset($article->category->name)){
            return $article->category->name;
        }
        $catTitle = $this->fetchCategoryTitle($article->catid);
        if($catTitle){
            return $catTitle;
        }else{
            $this->setError(self::$ERROR_NO_CATEGORY, 'No Category specified');
            return '';
        }
    }
    
//    public function truncate($text, $limit=  0){
//        // Truncate the item text if it is too long.
//        if ($limit > 0 && JString::strlen($text) > $limit){
//            // Find the first space within the allowed length.
//            $tmp = JString::substr($text, 0, $limit);
//            $offset = JString::strrpos($tmp, ' ');
//            if(JString::strrpos($tmp, '<') > JString::strrpos($tmp, '>')){
//                $offset = JString::strrpos($tmp, '<');
//            }
//            $tmp = JString::substr($tmp, 0, $offset);
//            // If we don't have 3 characters of room, go to the second space within the limit.
//            if (JString::strlen($tmp) >= $limit - 3) {
//                $tmp = JString::substr($tmp, 0, JString::strrpos($tmp, ' '));
//            }
//            //put all opened tags into an array
//            preg_match_all ( "#<([a-z][a-z0-9]?)( .*)?(?!/)>#iU", $tmp, $result );
//            $openedtags = $result[1];
//            $openedtags = array_diff($openedtags, array("img", "hr", "br"));
//            $openedtags = array_values($openedtags);
//            //put all closed tags into an array
//            preg_match_all ( "#</([a-z]+)>#iU", $tmp, $result );
//            $closedtags = $result[1];
//            $len_opened = count ( $openedtags );
//            //all tags are closed
//            if( count ( $closedtags ) == $len_opened ){
//                return $tmp.'...';
//            }
//            $openedtags = array_reverse ( $openedtags );
//            // close tags
//            for( $i = 0; $i < $len_opened; $i++ ){
//                if ( !in_array ( $openedtags[$i], $closedtags ) ){
//                    $tmp .= "</" . $openedtags[$i] . ">";
//                } else {
//                    unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
//                }
//            }
//            $text = $tmp.'...';
//        }
//        return $text;
//    }
    
//    public function getCurrentDate(){
//        return JFactory::getDate()->toMySQL();
//    }
    
    public function isPublished($article){
        $isPublished = $article->published == $this->STATE_PUBLISHED ? true : false;
        if(!$isPublished){
            return FALSE;
        }
        $publishUp = isset($article->publish_up) ? $article->publish_up : '';
        $publishDown = isset($article->publish_down) ? $article->publish_down : '';
        if($publishUp == '' ){
            return false;
        }
        $now = $this->getCurrentDate();
        if ( ($publishUp > $now) ){
            return FALSE;
        }else if($publishDown < $now && $publishDown != '0000-00-00 00:00:00' && $publishDown!=""){
            return FALSE;
        }else {
            return TRUE;
        }
    }
    
    public function isArticle(){
        $id = JRequest::getInt('id');
        $cid = JRequest::getInt('cid');
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');
        $hasID = is_int($id) ? true : false;
        $hasCID = is_int($cid) ? true : false;
        $isK2 = $option == 'com_k2' ? true : false;
        $isView = $view == 'item' ? true : false;
        if( ($hasID || $hasCID) && $isK2 && $isView ){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    public function tags($article) {
        $db = &JFactory::getDbo();
        $query = "SELECT ".$db->nameQuote('name').
                " FROM ".$db->nameQuote('#__k2_tags')." AS t".
                " INNER JOIN " . $db->nameQuote('#__k2_tags_xref')." AS x".
                " ON  x.tagID = t.id".
                " WHERE x.itemID = " . $db->Quote($article->id);
        $db->setQuery($query);
        $tags=  $db->loadResultArray ();
        return $tags;
    }

}
