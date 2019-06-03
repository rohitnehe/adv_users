<?php

//namespace common\components; no need to use it as it is registered at entry script

use yii\base\Component;
use common\models\User;

/**
 * CHelper Component
 *
 */
class CHelper extends Component {

    const FROM_API = 1;
    const FROM_WEB = 0;

    /**
     * Function added to display the formatted output on the screen
     * In controller use as $this->debug();
     * In view use as $this->context->debug();
     * @param type $variable
     * @param type $die
     */
    public function init() {
        parent::init();
    }

    /**
     * 
     * @param type $variable
     * @param type $die
     */
    public static function debug($variable, $die = true) {
        echo "<pre>";
        echo "<div class='row' style='display:table;color:white;background:#5C5C5C;'><div style='width: 50%; display: table-cell; vertical-align:top'>";
        echo "<h1 style='color:orange'>PRINT ARRAY</h1>";
        echo "<pre style='padding:10px 20px;border-right:1px solid white'>";
        print_r($variable);
        echo "</pre>";
        echo "</div>";
        echo "<div div style='width: 50%; display: table-cell; vertical-align:top'>";
        echo "<h1 style='color:orange'>VAR DUMP</h1>";
        echo "<pre style='padding:10px 20px;'>";
        var_dump($variable);
        echo "</pre>";
        echo "</div></div>";
        echo "</pre>";
        ( $die ) ? die() : '';
    }

    /** Function Name :      baseUrl()
     *  Description :        This function returns the base url of the web application's root folder
     *  Parameters :         No parameters
     */
    public static function baseUrl() {

        return yii\helpers\BaseUrl::base();
    }

    /**
     * 
     * @param type $key
     */
    public static function setFlashSuccess($key, $message = null) {
        return Yii::$app->session->setFlash('success', \Yii::t('app.success', $key, ['MESSAGE' => $message]));
    }

    /**
     * 
     * @param type $key
     */
    public static function setFlashError($key, $message = null) {
        Yii::$app->session->setFlash('error', \Yii::t('app.error', $key, ['MESSAGE' => $message]));
    }

    /**
     * 
     * @param type $key
     */
    public static function setFlashWarning($key, $message = null) {
        Yii::$app->session->setFlash('warning', \Yii::t('app.warning', $key, ['MESSAGE' => $message]));
    }

    /**
     * 
     * @param type $key
     */
    public static function setFlashNotice($key, $message = null) {
        Yii::$app->session->setFlash('notice', \Yii::t('app.notice', $key, ['MESSAGE' => $message]));
    }

    /**
     * @author Nikhil B <nikhilbhagunde@benchmarkitsolutions.com> 
     * @param type $attr
     * @return type
     */
    public static function getUserIdentityData($attr = '') {
        if (!isset(Yii::$app->user)) {
            return;
        }
        if (Yii::$app->user->getIdentity() === NULL)
            return;
        if ($attr != '')
            return Yii::$app->user->getIdentity()->getAttribute($attr);
        else {
            return Yii::$app->user->getIdentity()->getAttributes();
        }
    }

    /**
     * @author Prasad B <prasadbhale@benchmarkitsolutions.com> 
     * @param type $attr
     * @return type
     */
    public static function setFroentUserRedirection($user_type) {

        switch ($user_type) {
            case 'GA' :
            case 'GU' :
                $redirect_url = ['/growers/dashboard'];
                break;
            case 'CA':
            case 'CU' :
                $redirect_url = ['/customers/dashboard'];
                break;
            case 'SAL' :
                $redirect_url = ['/sales/dashboard'];
                break;
            case 'SA' :
            case 'A' :
                $redirect_url = ['/admin/index'];
                break;
        }

        return $redirect_url;
    }

    /**
     * @author Gajanan M <Gajananmahajan@benchmarkitsolutions.com> 
     * @param type $attr
     * @return get the current grower id(wether grower admin or user)type
     */
    public static function getCurrentGrowerId() {
        $current_user_info = Yii::$app->session->get('front_login'); // current logged in user id
        if ($current_user_info['front_login']['user_type'] == User::USER_TYPE_GROWER_ADMIN) {
            return $current_user_info['front_login']['user_id'];
        } else {
            return $current_user_info['front_login']['ref_user_id'];
        }
    }

    /**
     * @author Ravina Surve <ravinasurve@benchmarkitsolutions.com> 
     * @param type $attr
     * @return get the current customer id(wether grower admin or user)type
     */
    public static function getCurrentCustomerId() {
        $current_user_info = Yii::$app->session->get('front_login'); // current logged in user id
        if ($current_user_info['front_login']['user_type'] == User::USER_TYPE_CUSTOMER_ADMIN) {
            return $current_user_info['front_login']['user_id'];
        } else {
            return $current_user_info['front_login']['ref_user_id'];
        }
    }

    /**
     * @author Kanchan
     * @return get the current sales id
     */
    public static function getCurrentSalesId() {
        $current_user_info = Yii::$app->session->get('front_login'); // current logged in user id
        if ($current_user_info['front_login']['user_type'] == User::USER_SALES_REP) {
            return $current_user_info['front_login']['user_id'];
        } else {
            return $current_user_info['front_login']['ref_user_id'];
        }
    }
    
     /**
     * @author Kumar waghmode <kumarwaghmode@benchmarkitsolutions.com>
     * @date : 10 Dec 2018
     * @param type date
     * @return type
     */
    public static function getFormatedDatetime($date) {
        $formatter = \Yii::$app->formatter;
        $_date = str_replace('-', '/', $date);
        return $date ? $formatter->asDate($_date, 'php:m-d-Y H:i:s') : '';
    }
}
