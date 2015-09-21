<?php
namespace Adapter\Util;

use Adapter\Globals\ServiceConstant;

class Calypso
{
    private static $instance = null;
    private $httpReqPostData = null;

    private $httpReqGetData = null;
    private $httpReqFileData = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            return self::$instance = new Calypso();
        }
        return self::$instance;
    }

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

    public function formatCurrency($value,$dp=2)
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

    public function unsetSession($key = null)
    {
        if ($key != null) {
            unset($_SESSION[$key]);
        } else {
            session_destroy();
        }
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
        if(empty(trim($value))) {
            return $default;
        }
        return $value;
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

    public static function permissionMap()
    {
        $permissionMap = [
            ServiceConstant::USER_TYPE_ADMIN => [],
            ServiceConstant::USER_TYPE_OFFICER => ['finance/*', 'billing/*', 'admin/*'],
            ServiceConstant::USER_TYPE_SWEEPER => ['site/*', 'parcels/*', 'shipments/*', 'hubs/*', 'finance/*', 'billing/*', 'admin/*'],
            ServiceConstant::USER_TYPE_DISPATCHER => ['site/*', 'parcels/*', 'shipments/*', 'hubs/*', 'finance/*', 'billing/*', 'admin/*'],
            ServiceConstant::USER_TYPE_GROUNDSMAN => [
                'parcels/*',
                'shipments/forsweep',
                'shipments/delivered',
                'hubs/hubarrival',
                'finance/*',
                'billing/*',
                'admin/*'],
        ];
        return $permissionMap;
    }

    public static function normaliseLinkLabel($label)
    {
        return str_replace('_', ' ', $label);
    }

    public static function getMenus()
    {
        $menus = [
            'Dashboard' => ['base' => 'site', 'base_link' => 'site/index','class' => 'fa fa-dashboard'],
            'New_Shipments' => ['base_link' => 'shipments/processed', 'class' => '','branch' => [ ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
            'Receive_Shipments' => ['base_link' => 'hubs/hubarrival', 'class' => '','branch' => [ ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
            'Ready_for_Sorting' => ['base_link' => 'hubs/destination', 'class' => '','branch' => [ ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
            'Sorted_Shipments' => ['base_link' => 'hubs/delivery', 'class' => '','branch' => [ ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
            'Due_for_Delivery' => ['base_link' => 'shipments/fordelivery', 'class' => '','branch' => [ ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
            'Due_for_Sweep' => ['base_link' => 'shipments/forsweep', 'class' => '', 'branch' => [ ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HQ]],
            'Direct_Delivery' => ['base_link' => 'shipments/dispatched', 'class' => '','branch' => [ ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
            'Dispatched_to_Branches' => ['base_link' => 'hubs/hubdispatch', 'class' => '','branch' => [ ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
            'Delivered' => ['base_link' => 'shipments/delivered', 'class' => '','branch' => [ ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
            'All_Shipments' => ['base_link' => 'shipments/all', 'class' => '','branch' => [ ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]],
            'Administrator' => ['base' => 'admin', 'class' => 'fa fa-user', 'base_link' => [
                'Manage_branches' => ['base_link' => 'admin/managebranches', 'class' => ''],
                'Manage_staff_accounts' => ['base_link' => 'admin/managestaff', 'class' => ''],
                'Company_registration' => ['base_link' => 'admin/companies', 'class' => ''],
                'Billing' => ['base' => 'billing', 'class' => '', 'base_link' => [
                    'View_Matrix' => ['base_link' => 'billing/matrix', 'class' => ''],
                    'Zones' => ['base_link' => 'billing/zones', 'class' => ''],
                    'Regions' => ['base_link' => 'billing/regions', 'class' => ''],
                    'State_-_Region_Mapping' => ['base_link' => 'billing/statemapping', 'class' => ''],
                    'City_-_State Mapping' => ['base_link' => 'billing/citymapping', 'class' => ''],
                    'Weight_Ranges' => ['base_link' => 'billing/weightranges', 'class' => ''],
                    'Pricing' => ['base_link' => 'billing/pricing', 'class' => ''],
                    'Onforwarding_Charges' => ['base_link' => 'billing/onforwarding', 'class' => ''],
                ],'branch' => [ ServiceConstant::BRANCH_TYPE_HQ]]
            ],'branch' => [ ServiceConstant::BRANCH_TYPE_HQ]],
            'Parcel History' => ['base' => 'track', 'base_link' => 'track/','class' => 'fa fa-gift'],
            'Manifests' => ['base' => 'manifest', 'base_link' => 'manifest/index','class' => 'fa fa-book'],
            'Customer_History' => ['base' => 'shipments', 'base_link' => 'shipments/customerhistory','class' => 'fa fa-user'
                ,'branch' => [ ServiceConstant::BRANCH_TYPE_EC, ServiceConstant::BRANCH_TYPE_HUB, ServiceConstant::BRANCH_TYPE_HQ]] ,
            'Reconciliations' => ['base' => 'finance', 'class' => 'fa fa-money', 'base_link' =>[
                'Customers' => ['base_link' => 'finance/customersall', 'class' => ''],
                'Merchants' => ['base_link' => 'finance/merchantsdue', 'class' => '']
            ],'branch' => [ ServiceConstant::BRANCH_TYPE_HQ]]
        ];
        return $menus;
    }

    public static function canAccess($role,$link)
    {
        $permissions = self::permissionMap();
        if (!array_key_exists($role, $permissions)) return false;

        $current_user_permission = $permissions[$role];
        $link_temp = explode('/',$link);
        if(in_array($link_temp[0].'/*',$current_user_permission))
        {
            return false;
        }

        if(in_array($link,$current_user_permission))
        {
            return false;
        }
        return true;
    }
}
