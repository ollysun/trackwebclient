<?php
namespace Adapter\Util;

use Adapter\Globals\ServiceConstant;
use yii\helpers\Url;

class Calypso
{
    private static $instance = null;
    private $httpReqPostData = null;

    private $httpReqGetData = null;
    private $httpReqFileData = null;

    public function __construct()
    {
        if (isset($_POST) && !empty($_POST)) {
            $this->httpReqPostData = json_decode(json_encode($_POST), false);
        } else {
            $this->httpReqPostData = new \stdClass();
        }

        if (isset($_GET) && !empty($_GET)) {
            $this->httpReqGetData = json_decode(json_encode($_GET), false);
        } else {
            $this->httpReqGetData = new \stdClass();
        }

        if (isset($_FILES)) {
            $this->httpReqFileData = json_decode(json_encode($_FILES), false);
        } else {
            $this->httpReqFileData = new \stdClass();
        }
    }

    public static function getCurrentBranchType(){
        $user = Calypso::getInstance()->session('user_session');
        if(isset($user) && isset($user['branch'])){
            return $user['branch']['branch_type'];
        }
        return null;
    }

    public static function isCooperateUser(){
        $user = self::getInstance()->session('user_session');
        return isset($user['company_id']);
    }

    public static function userIsInRole($role_id){
        return self::getInstance()->session('user_session')['role']['id'] == $role_id;
    }


    /**
     * Get's a value if it's non empty
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $array
     * @param $key
     * @param null $default
     * @return null
     */
    public static function getDisplayValue($array, $key, $default = null)
    {
        $value = self::getValue($array, $key, null);
        if (empty(trim($value))) {
            return $default;
        }
        return $value;
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
        if (!isset($array)) {
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

    public static function showFlashMessages()
    {
        $flashMessages = '';
        $allMessages = \Yii::$app->session->getAllFlashes();
        foreach ($allMessages as $key => $message) {
            $flashMessages .= '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
        \Yii::$app->session->removeAllFlashes();
        return $flashMessages;
    }

    public static function normaliseLinkLabel($label)
    {
        return str_replace('_', ' ', $label);
    }

    public static function isActiveMenu($menu){
        $curPage = \Yii::$app->controller->id.'/'.\Yii::$app->requestedAction->id;
        $curPageWithModule = \Yii::$app->controller->module->id.'/'.\Yii::$app->controller->id.'/'.\Yii::$app->requestedAction->id;
        $isActiveMenu = false;
        if(!is_array($menu['base_link'])){
            if(\Yii::$app->requestedAction->id == 'index'){
                if(!endsWith($menu['base_link'], 'index')){
                    $menu['base_link'] .= endsWith($menu['base_link'], '/')?'index': '/index';
                }
            }
            $isActiveMenu = Url::toRoute($curPage) == Url::toRoute($menu['base_link']) ||
                Url::toRoute($curPageWithModule) == Url::toRoute($menu['base_link']);
        }else{
            foreach ($menu['base_link'] as $item) {
                if(self::isActiveMenu($item)){
                    $isActiveMenu = true;
                    break;
                }
            }
        }
        return $isActiveMenu;
    }

    /*
    static public function isActiveMenu($action = null, $controller = null, $module = null)
    {
        $action = empty($action)? \Yii::$app->controller->action->id:$action;
        $controller = empty($controller)? \Yii::$app->controller->id:$controller;
        $module = empty($module)? \Yii::$app->controller->module->id:$module;

        return $action == \Yii::$app->controller->action->id && $controller == \Yii::$app->controller->id
        && $module == \Yii::$app->controller->module->id;
    }
    */

    public static function getMenus()
    {
        $menus = [
            'Dashboard' => ['base' => 'site', 'base_link' => 'site/index', 'class' => 'fa fa-dashboard'],
            'Shipments' => ['base' => 'shipments', 'class' => 'fa fa-car', 'base_link' => [
                'New_Shipments' => ['base_link' => 'shipments/processed', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ, ServiceConstant::BRANCH_TYPE_COMPANY]],
                'Receive_Shipments' => ['base_link' => 'hubs/hubarrival', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
                'Ready_for_Sorting' => ['base_link' => 'hubs/destination', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
                'Ready_for_Sorting_G-man' => ['base_link' => 'hubs/destination-groundsman', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_HUB]],
                'Expected_Shipments' => ['base_link' => 'hubs/expected', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_HUB]],
                'Draft_Sortings' => ['base_link' => 'hubs/draftsortings', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_HUB]],
                'Bulk_Shipment_Tasks' => ['base_link' => 'shipments/bulk', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HQ]],
                'Sorted_Shipments' => ['base_link' => 'hubs/delivery', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
                'Due_for_Delivery' => ['base_link' => 'shipments/fordelivery', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
                'Due_for_Sweep' => ['base_link' => 'shipments/forsweep', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HQ]],
                'Direct_Delivery' => ['base_link' => 'shipments/dispatched', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
                'Dispatched_to_Branches' => ['base_link' => 'hubs/hubdispatch', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
                'Delivered' => ['base_link' => 'shipments/delivered', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
                'Returned' => ['base_link' => 'shipments/returned', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
                'All_Shipments' => ['base_link' => 'shipments/all', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ, ServiceConstant::BRANCH_TYPE_COMPANY]],
                'Shipment_Exceptions' => ['base' => 'report', 'base_link' => 'shipments/exceptions', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_HQ]],
                'Delayed_Shipments' => ['base' => 'report', 'base_link' => 'shipments/delayedshipments', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_HQ]],
                'Report' => ['base' => 'report', 'base_link' => 'shipments/report', 'class' => 'fa fa-book', 'branch' => [ServiceConstant::BRANCH_TYPE_HQ, ServiceConstant::BRANCH_TYPE_COMPANY, ServiceConstant::USER_TYPE_OFFICER, ServiceConstant::USER_TYPE_COMPANY_ADMIN]]
            ], 'corporate' => true],

            'Administrator' => ['base' => 'admin', 'class' => 'fa fa-user', 'base_link' => [
                'Manage_branches' => ['base_link' => 'admin/managebranches', 'class' => ''],
                'Manage_Transit_Time' => ['base_link' => 'admin/managetransittime', 'class' => ''],
                'Manage_cities' => ['base_link' => 'admin/managecities', 'class' => ''],
                'Manage_routes' => ['base_link' => 'admin/manageroutes', 'class' => ''],
                'Manage_staff_accounts' => ['base_link' => 'admin/managestaff', 'class' => ''],
                'Company_Registration' => ['base_link' => 'admin/companies', 'class' => ''],
                'Company_Express_Centre' => ['base_link' => 'admin/companyecs', 'class' => ''],
                'Billing' => ['base' => 'billing', 'class' => '', 'base_link' => [
                    'View_Matrix' => ['base_link' => 'billing/matrix', 'class' => ''],
                    'Zones' => ['base_link' => 'billing/zones', 'class' => ''],
                    'Regions' => ['base_link' => 'billing/regions', 'class' => ''],
                    'State_-_Region_Mapping' => ['base_link' => 'billing/statemapping', 'class' => ''],
                    'City_-_State Mapping' => ['base_link' => 'billing/citymapping', 'class' => ''],
                    'Weight_Ranges' => ['base_link' => 'billing/weightranges', 'class' => ''],
                    'Pricing' => ['base_link' => 'billing/pricing', 'class' => ''],
                    'Onforwarding_Charges' => ['base_link' => 'billing/onforwarding', 'class' => ''],
                    'Corporate_Billing' => ['base_link' => 'billing/corporate', 'class' => '']
                ], 'branch' => [ServiceConstant::BRANCH_TYPE_HQ]],
                'Audit_Trail' => ['base_link' => 'admin/audittrail', 'class' => ''],
            ], 'branch' => [ServiceConstant::BRANCH_TYPE_HQ]],
            'Parcel History' => [
                'base' => 'track',
                'base_link' => 'track/',
                'class' => 'fa fa-gift',
                'corporate' => true
            ],
            'Manifests' => ['base' => 'manifest', 'base_link' => 'manifest/index', 'class' => 'fa fa-book'],
            'Customer_History' => ['base' => 'shipments', 'base_link' => 'shipments/customerhistory', 'class' => 'fa fa-user'
                , 'branch' => [ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
            'Finance' => [
                'base' => 'finance', 'class' => 'fa fa-money', 'base_link' => [
                    'Corporate Shipments' => ['base_link' => 'finance/corporateshipment', 'class' => ''],
                    'Credit Note' => ['base_link' => 'finance/creditnote', 'class' => ''],
                    'Customers' => ['base_link' => 'finance/customersall', 'class' => ''],
                    'Invoice' => ['base_link' => 'finance/invoice', 'class' => ''],
                    'Bulk Invoice Tasks' => ['base_link' => 'finance/bulkinvoicetasks', 'class' => ''],
                    'Merchants' => ['base_link' => 'finance/merchantsdue', 'class' => ''],
                ],
                'branch' => [ServiceConstant::BRANCH_TYPE_HQ]
            ],
            'Corporate' => [
                'base' => 'request', 'class' => 'fa fa-gift', 'base_link' => [
                    //'New_Shipment' => ['base_link' => 'corporate/shipments/new', 'class' => ''],
                    //'Shipment_Report' => ['base_link' => 'corporate/shipments/all', 'class' => ''],
                    'Shipment_Requests' => ['base_link' => 'corporate/request/shipments', 'class' => ''],
                    'Pickup_Requests' => ['base_link' => 'corporate/request/pickups', 'class' => ''],
                    'Users' => ['base_link' => 'corporate/users', 'class' => ''],
                    'Pending Shipments' => ['base_link' => 'corporate/pending/shipments', 'class' => ''],
                    'Pending Pickups' => ['base_link' => 'corporate/pending/pickups', 'class' => '', 'branch' => [ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]]
                ],
                'corporate' => true
            ]
        ];
        return $menus;
    }

    public static function canAccess($role, $link)
    {
        $permissions = self::permissionMap();
        if (!array_key_exists($role, $permissions)) return false;

        $current_user_permission = $permissions[$role];
        $link_temp = explode('/', $link);
        if (in_array($link_temp[0] . '/*', $current_user_permission)) {
            return false;
        }

        if (in_array($link, $current_user_permission)) {
            return false;
        }
        return true;
    }

    public static function permissionMap()
    {
        $permissionMap = [
            ServiceConstant::USER_TYPE_SUPER_ADMIN => self::getCorporateRoutes(),
            ServiceConstant::USER_TYPE_ADMIN => self::getCorporateRoutes(),
            ServiceConstant::USER_TYPE_OFFICER => array_merge(
                ['finance/*', 'billing/*', 'admin/*']
                , self::getCorporateRoutes()),
            ServiceConstant::USER_TYPE_SWEEPER => array_merge(
                ['site/index', 'site/newparcel', 'shipments/bulk', 'manifest/index', 'shipments/dispatched', 'shipments/returned', 'shipments/fordelivery', 'finance/*', 'billing/*', 'admin/*', 'corporate/request/pending',
                    'hubs/hubarrival', 'hubs/destination', 'hubs/destination-groundsman', 'hubs/expected', 'hubs/draftsortings', 'hubs/hubdispatch'
                ]
                , self::getCorporateRoutes()),
            ServiceConstant::USER_TYPE_DISPATCHER => array_merge(
                ['site/*', 'manifest/index', 'shipments/dispatched', 'shipments/returned', 'shipments/fordelivery', 'hubs/*', 'finance/*', 'billing/*', 'admin/*', 'corporate/request/pending']
                , self::getCorporateRoutes()
            ),
            ServiceConstant::USER_TYPE_GROUNDSMAN => array_merge([
                'parcels/*',
                'shipments/forsweep',
                'shipments/delivered',
                'shipments/report',
                'hubs/hubarrival',
                'finance/*',
                'billing/*',
                'admin/*',
                'corporate/pending/shipments',
                'corporate/pending/pickups'
            ], self::getCorporateRoutes()),
            ServiceConstant::USER_TYPE_COMPANY_ADMIN => [
                'corporate/pending/shipments',
                'corporate/pending/pickups',
                'site/*'
            ],
            ServiceConstant::USER_TYPE_COMPANY_OFFICER => [
                'corporate/users',
                'corporate/pending/shipments',
                'corporate/pending/pickups',
                'site/*'
            ],
            ServiceConstant::USER_TYPE_SALES_AGENT => array_merge([
                'finance/*', 'billing/*', 'admin/*', 'manifest/*', 'shipments/dispatched', 'shipments/delivered', 'shipments/returned', 'shipments/fordelivery', 'hubs/*', 'corporate/users'
            ], self::getCorporateRoutes())
        ];
        return $permissionMap;
    }

    /**
     * Returns an array of Corporate only routes
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public static function getCorporateRoutes()
    {
        return [
            'corporate/request/shipments',
            'corporate/request/pickups',
            'corporate/shipments/report',
            //'corporate/shipments/new',
            //'corporate/shipments/all',
        ];
    }

    /**
     * Make a page that can possibly contain a bagged parcel a referrer page
     * @author Akintewe Rotimi <akintewe.rotimi@gmail.com>
     */
    public static function makeAnUnbagReferrer()
    {
        //set unbag referrer
        $unbag_referrer = \Yii::$app->request->getUrl();
        Calypso::getInstance()->session('unbag_referrer', $unbag_referrer);
    }

    public function session($key, $value = NULL)
    {
        if (isset($_SESSION)) {
            if ($key && $value != NULL) {
                $_SESSION[$key] = $value;
            } elseif ($key && $value == NULL && isset($_SESSION[$key])) {
                return $_SESSION[$key];
            }
        }
        return false;
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            return self::$instance = new Calypso();
        }
        return self::$instance;
    }

    /**
     * Returns the referrer page for a page that an unbag / open bag action was carried out
     * @author Akintewe Rotimi <akintewe.rotimi@gmail.com>
     * @return string
     */
    public static function getUnbagReferrer()
    {
        //get unbag referrer
        $unbag_referrer = Calypso::getInstance()->session('unbag_referrer');
        if (empty($unbag_referrer)) {
            $unbag_referrer = ServiceConstant::DEFAULT_UNBAG_REFERRER;
        }
        return $unbag_referrer;
    }

    public function post()
    {
        try {
            return $this->httpReqPostData;
        } catch (\Exception $ex) {
            return false;
        }

    }

    public function get()
    {

        try {
            return $this->httpReqGetData;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function file()
    {
        try {
            return $this->httpReqFileData;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function upload($filename, $directory)
    {
        return @move_uploaded_file($filename, $directory);
    }

    public function flashErrorMsg()
    {
        $error = $this->session('error_msg');
        $this->unsetSession('error_msg');
        return $error;
    }

    public function unsetSession($key = null)
    {
        if ($key != null) {
            unset($_SESSION[$key]);
        } else {
            session_destroy();
        }
    }

    public function flashSuccessMsg()
    {
        $error = $this->session('success_msg');
        $this->unsetSession('success_msg');
        return $error;
    }

    public function setPageData($data)
    {
        $this->session('PAGE_DATA', $data);
    }

    public function getPageData()
    {
        $data = $this->session('PAGE_DATA');
        $this->unsetSession('PAGE_DATA');
        return $data;
    }

    public function setFlashErrorMsg($message)
    {
        $this->session('error_msg', $message);
    }

    public function setFlashSuccessMsg($message)
    {
        $this->session('success_msg', $message);
    }

    public function formatCurrency($value, $dp = 2)
    {
        if (intval($value) <= 0) return $value;
        $decimal_holder = explode('.', $value);
        $value_arr = str_split($decimal_holder[0]);
        if (count($value_arr) <= 3) return $value;
        $final_value = number_format($value, $dp, ".", ",");
        return $final_value;
    }

    public function formatWeight($value)
    {
        if (intval($value) <= 0) return $value;
        $decimal_holder = explode('.', $value);
        $value_arr = str_split($decimal_holder[0]);
        if (count($value_arr) <= 3) return $value;
        $final_value = number_format($value, 0, ".", ",");
        return $final_value;
    }

    public function cookie($key, $value = NULL, $expires = null)
    {
        if (isset($_COOKIE)) {
            if ($key && $value != NULL) {
                setcookie($key, $value);
            } elseif ($key && $value == NULL && isset($_COOKIE[$key])) {
                return $_COOKIE[$key];
            }
        }
        return false;
    }

    public function isLoggedIn()
    {
        return $this->session('loggedin');
    }

    /**
     * @param $decrypted
     * @param $password
     * @param string $salt
     * @return bool|string
     *
     * pulled from php.net
     * http://php.net/manual/en/book.mcrypt.php
     */
    public function encrypt($decrypted, $password = '5ok@0moOlopeQQ', $salt = '!kQm*fF3pXe1Kbm%9')
    {
        // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
        $key = hash('SHA256', $salt . $password, true);
        // Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
        srand();
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_RAND);
        if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;
        // Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
        // We're done!
        return $iv_base64 . $encrypted;
    }

    /**
     * @param $encrypted
     * @param $password
     * @param string $salt
     * @return bool|string
     *
     * pulled from php.net
     * http://php.net/manual/en/book.mcrypt.php
     */
    public function decrypt($encrypted, $password = '5ok@0moOlopeQQ', $salt = '!kQm*fF3pXe1Kbm%9')
    {
        // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
        $key = hash('SHA256', $salt . $password, true);
        // Retrieve $iv which is the first 22 characters plus ==, base64_decoded.
        $iv = base64_decode(substr($encrypted, 0, 22) . '==');
        // Remove $iv from $encrypted.
        $encrypted = substr($encrypted, 22);
        // Decrypt the data.  rtrim won't corrupt the data because the last 32 characters are the md5 hash; thus any \0 character has to be padding.
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
        // Retrieve $hash which is the last 32 characters of $decrypted.
        $hash = substr($decrypted, -32);
        // Remove the last 32 characters from $decrypted.
        $decrypted = substr($decrypted, 0, -32);
        // Integrity check.  If this fails, either the data is corrupted, or the password/salt was incorrect.
        if (md5($decrypted) != $hash) return false;
        return $decrypted;
    }

    public function setIsLoggedIn()
    {
        return $this->session('loggedin', true);
    }

    public function redirect($path)
    {
        header('location:' . $path);
    }

    public function AppRedirect($controller, $action = 'index', $args = null)
    {
        $str = '';
        if ($args != null) {
            if (is_array($args)) {
                $str = join('/', $args);
            } else {
                $str = $args;
            }
        }
        header('location:' . ServiceConstant::BASE_PATH . '/' . $controller . '/' . $action . '/' . $str);
    }
}
