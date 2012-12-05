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

class JArticle15 extends JArticle{
    
    public $JOOMLA_VERSION = '1.5';
    public $ACCESS_PUBLIC = '0';
    public $ACCESS_REGISTRED = '1';
    public $ACCESS_SPECIAL = '2';
    public $STATE_ARCHIVED = '-1';
    
    public function __construct($article) {
        parent::__construct($article);
    }
    
    protected function loadDependencies(){
        return;
    }
    
    public function categoryTitle($article){
        if(isset($article->category)){
            return $article->category;
        }else{
            $this->setError(self::$ERROR_NO_CATEGORY, 'No Category specified');
            return '';
        }
    }
    
    public function truncate($text, $limit=  0){
        // Truncate the item text if it is too long.
        if ($limit > 0 && JString::strlen($text) > $limit){
            // Find the first space within the allowed length.
            $tmp = JString::substr($text, 0, $limit);
            $offset = JString::strrpos($tmp, ' ');
            if(JString::strrpos($tmp, '<') > JString::strrpos($tmp, '>')){
                $offset = JString::strrpos($tmp, '<');
            }
            $tmp = JString::substr($tmp, 0, $offset);
            // If we don't have 3 characters of room, go to the second space within the limit.
            if (JString::strlen($tmp) >= $limit - 3) {
                $tmp = JString::substr($tmp, 0, JString::strrpos($tmp, ' '));
            }
            //put all opened tags into an array
            preg_match_all ( "#<([a-z][a-z0-9]?)( .*)?(?!/)>#iU", $tmp, $result );
            $openedtags = $result[1];
            $openedtags = array_diff($openedtags, array("img", "hr", "br"));
            $openedtags = array_values($openedtags);
            //put all closed tags into an array
            preg_match_all ( "#</([a-z]+)>#iU", $tmp, $result );
            $closedtags = $result[1];
            $len_opened = count ( $openedtags );
            //all tags are closed
            if( count ( $closedtags ) == $len_opened ){
                return $tmp.'...';
            }
            $openedtags = array_reverse ( $openedtags );
            // close tags
            for( $i = 0; $i < $len_opened; $i++ ){
                if ( !in_array ( $openedtags[$i], $closedtags ) ){
                    $tmp .= "</" . $openedtags[$i] . ">";
                } else {
                    unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
                }
            }
            $text = $tmp.'...';
        }
        return $text;
    }
    
    public function getCurrentDate(){
        return JFactory::getDate()->toMySQL();
    }
}
