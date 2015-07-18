<?php
namespace Adapter\Util;

use Adapter\Globals\ServiceConstant;

class Calypso{
    private static $instance = null;
    private $httpReqPostData = null;

    private $httpReqGetData = null;
    private $httpReqFileData = null;
    public static function getInstance(){
        if(self::$instance==null){
            return self::$instance = new Calypso();
        }
        return self::$instance;
    }

    public function __construct() {
        if(isset($_POST) && !empty($_POST)){
            $this->httpReqPostData = json_decode(json_encode($_POST),false);
        }else{
            $this->httpReqPostData = new \stdClass();
        }

        if(isset($_GET) && !empty($_GET)){
            $this->httpReqGetData = json_decode(json_encode($_GET),false);
        }else{
            $this->httpReqGetData = new \stdClass();
        }

        if(isset($_FILES)){
            $this->httpReqFileData = json_decode(json_encode($_FILES),false);
        }else{
            $this->httpReqFileData = new \stdClass();
        }
    }

    public function post(){
        try{
            return $this->httpReqPostData;
        }catch (\Exception $ex){
            return false;
        }

    }
    public function get(){

        try{
            return $this->httpReqGetData;
        }catch (\Exception $ex){
            return false;
        }
    }
    public function file(){
        try{
            return $this->httpReqFileData;
        }catch (\Exception $ex){
            return false;
        }
    }
    public function upload($filename,$directory){
        return @move_uploaded_file($filename,$directory);
    }
    public function flashErrorMsg(){
        $error = $this->session('error_msg');
        $this->unsetSession('error_msg');
        return $error;
    }
    public function flashSuccessMsg(){
        $error = $this->session('success_msg');
        $this->unsetSession('success_msg');
        return $error;
    }
    public function setPageData($data){
        $this->session('PAGE_DATA',$data);
    }
    public function getPageData(){
        $data = $this->session('PAGE_DATA');
        $this->unsetSession('PAGE_DATA');
        return $data;
    }
    public function setFlashErrorMsg($message){
        $this->session('error_msg',$message);
    }
    public function setFlashSuccessMsg($message){
        $this->session('success_msg',$message);
    }
    public function session($key,$value=NULL){
        if(isset($_SESSION)){
            if($key && $value != NULL){
                $_SESSION[$key] = $value;
            }
            elseif($key && $value==NULL && isset($_SESSION[$key])){
                return $_SESSION[$key];
            }
        }
        return false;
    }
    public function isLoggedIn(){
        return $this->session('loggedin');
    }
    public function setIsLoggedIn(){
        return $this->session('loggedin',true);
    }
    public function unsetSession($key=null){
        if($key!=null){
            unset($_SESSION[$key]);
        }else{
            session_destroy();
        }
    }
    public function redirect($path){
        header('location:'.$path);
    }
    public function AppRedirect($controller,$action='index',$args=null){
        $str = '';
        if($args != null){
            if(is_array($args)){
                $str = join('/',$args);
            }else{ $str = $args;}
        }
        header('location:'.ServiceConstant::BASE_PATH.'/'.$controller.'/'.$action.'/'.$str);
    }

    /**
     * Gets value from array or object
     * Copied from Yii2 framework
     * @link http://www.yiiframework.com/
     * @copyright Copyright (c) 2008 Yii Software LLC
     * @license http://www.yiiframework.com/license/
     * @param      $array
     * @param      $key
     * @param null $default
     * @return null
     * @author Qiang Xue <qiang.xue@gmail.com>
     * @author Adegoke Obasa <adegoke.obasa@konga.com>
     * @author Rotimi Akintewe <rotimi.akintewe@konga.com>
     */
    public static function getValue($array, $key, $default = null)
    {
        if(!isset($array)) {
            return $default;
        }

        if ($key instanceof \Closure) {
            return $key($array, $default);
        }
        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }
        if (is_array($array) && array_key_exists($key, $array)) {
            return $array[$key];
        }
        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }
        if (is_object($array) && property_exists($array, $key)) {
            return $array->$key;
        } elseif (is_array($array)) {
            return array_key_exists($key, $array) ? $array[$key] : $default;
        } else {
            return $default;
        }
    }

    public static function showFlashMessages() {
        $flashMessages = '';
        $allMessages = \Yii::$app->session->getAllFlashes();
        foreach ($allMessages as $key => $message) {
            $flashMessages .= '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
        return $flashMessages;
    }
}
