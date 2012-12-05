<?php
/**
 * Description of EventDebugger30
 *
 * @version  1.0
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2012-dec-05 Stilero Webdesign (www.stilero.com)
 * @category Plugins
 * @license	GPLv2
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

// import library dependencies
jimport('joomla.plugin.plugin');
print $classes = dirname(__FILE__).'/eventdebugger30/classes/';
JLoader::register('JArticleErrors', $classes.'jarticle-errors.php');
JLoader::register('JArticleImage', $classes.'jarticle-image.php');
JLoader::register('JArticleUrl', $classes.'jarticle-url.php');
JLoader::register('K2JArticleUrl', $classes.'k2jarticle-url.php');
JLoader::register('JArticle', $classes.'jarticle.php');
JLoader::register('JArticle30', $classes.'jarticle30.php');
JLoader::register('K2JArticle', $classes.'k2jarticle.php');
JLoader::register('ShareLogger', $classes.'share-logger.php');
JLoader::register('ShareLogger30', $classes.'share-logger30.php');
JLoader::register('ShareErrors', $classes.'share-errors.php');
JLoader::register('ShareChecker', $classes.'share-checker.php');
JLoader::register('ShareChecker30', $classes.'share-checker30.php');

class plgContentEventdebugger30 extends JPlugin {
    
//    var $config;
    var $errors;
    protected $_JArticle;
    protected $_Checker;

//    function plgContentEventdebugger30 ( &$subject, $config ) {
//        parent::__construct( $subject, $config );
//    }
    
    public function debugArticle(&$article){
        $this->_JArticle = new JArticle($article);
        print "<pre>";
        var_dump($this->_JArticle->getArticle());
        print "</pre>";
        //exit;
        if($this->_JArticle->isArticle()){
            if($this->isReadyToShare()){
                $JAUrl = new JArticleUrl($this->_JArticle);
                $JAImage = new JArticleImage($this->_JArticle);
                print "<pre>";
                print $JAUrl->url();
                print "</pre>";
                $this->_Checker->log();
                exit;
                return '';
            }else{
                print "<pre>";
                var_dump($this->errors);
                print "</pre>";
                exit;
                return '';
            }
            $JAUrl = new JArticleUrl15($this->_JArticle);
            $JAImage = new JArticleImage($this->_JArticle);
            print "<pre>";
            print $JAUrl->url();
            //var_dump($article);
            //var_dump($JAImage->src());
            //print $JA->getArticle();exit;
            //var_dump($jarticle);exit;
            print "</pre>";
            return '';
        }
    }
    
    public function debugK2Article(&$article){
        $this->_JArticle = new K2JArticle($article);
        if($this->_JArticle->isArticle()){
             if($this->isReadyToShare()){
                $JAUrl = new K2JArticleUrl($this->_JArticle);
                $JAImage = new JArticleImage($this->_JArticle);
                print "<pre>";
                print $JAUrl->url();
                print "\n\r";
                print $JAImage->src();
                print "</pre>";
                //$this->_Checker->log();
                exit;
                return '';
            }else{
                print "<pre>";
                var_dump($this->errors);
                print "</pre>";
                //exit;
                return '';
            }
            $JAUrl = new JArticleUrl15($this->_JArticle);
            $JAImage = new JArticleImage($this->_JArticle);
            print "<pre>";
            print $JAUrl->url();
            //var_dump($article);
            //var_dump($JAImage->src());
            //print $JA->getArticle();exit;
            //var_dump($jarticle);exit;
            print "</pre>";
            return '';
        }
    }
    
    protected function isReadyToShare(){
        $categories = '';
        $categoriesFunction = 0;
        $delay = 1;
        $this->_Checker = new ShareChecker30($this->_JArticle, '#__sharelog', 'PLG_EVENTCHECKER_', '2012-11-13 08:00:00', $categories, $categoriesFunction, $delay);
        $this->_Checker->isArticle();
        $this->_Checker->isNewEnough();
        $this->_Checker->isPublic();
        $this->_Checker->isPublished();
        $this->_Checker->isShared();
        $this->_Checker->isTooEarly();
        $hasError = $this->_Checker->hasError();
        if($hasError){
            $this->errors = $this->_Checker->errors;
            return FALSE;
        }
        return TRUE;
    }
    
    
    public function onContentAfterDisplay($context, $article, &$params, $limitstart=0){
        $this->debugArticle($article);
        return '';
    }
    
    /**
     * Method is called right after the content is saved
     * 
     * @var string  $context    The context of the content passed to the plugin
     * @var object  $article    JTableContent object
     * @var bool    $isNew      If the content is just about to be created
     * @return void
     * @since 1.6
     */

    public function onContentAfterSave($context, $article, $isNew){
        $option = JRequest::getCmd('option');
        if($option == 'com_content'){
            $this->debugArticle($article);
        }elseif($option == 'com_k2'){
            $this->debugK2Article($article);
        }
        return true;
    }
    

} //End Class