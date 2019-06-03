<?php

use yii\rest\ActiveController;
use yii\web\Response;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RestHelper extends ActiveController {
    
    /**
     * @author Kumar Waghmode <kumarwaghmode@benchmarkitsolutions.com>
     * @date : 29 May 2019
     * @purpose : Function used to format the response as per the requirement, i.e. custome code, message, response data
     * @param int $code
     * @param type $message
     * @param array $data
     * @return type
     */
     public static function formatResponseSuccess($message_id, $data,$single_obj=false) {
        $code = Yii::$app->params['SUCCESS_CODE'];
//        if (empty($data)) $data = [];
        if($single_obj==false){
            if (!empty($data)  && (count($data) === count($data, COUNT_RECURSIVE))) {
                $data = [$data]; // is not multidimensional
            }
        }
        
        $message = 'Working';//CHelper::getSystemMessage($message_id,'', 'api');
        if (!empty($data)) {
            return Yii::createObject([
                        'class' => 'yii\web\Response',
                        'format' => Response::FORMAT_JSON,
                        'data' => [
                            'code' => $code,
                            'message' => !empty($message) ? $message : '',
                            'data' => $data
                        ],
            ]);
        } else {
             $message = 'Error Please Contact to Admin.';
            return Yii::createObject([
                        'class' => 'yii\web\Response',
                        'format' => Response::FORMAT_JSON,
                        'data' => [
                            'code' => $code,
                            'message' => !empty($message) ? $message : '',
                        //             'data' => $data
                        ],
            ]);
        }
    }
    
    /**
     * 
     * @param type $message_id
     * @param type $data
     * @return type
     */
    public static function formatResponseError($message_id, $data) {
        $code = Yii::$app->params['ERROR_CODE'];

        $message = 'Error Please Contact to Admin.'; //CHelper::getSystemMessage($message_id,'', 'api');

        return Yii::createObject([
                    'class' => 'yii\web\Response',
                    'format' => Response::FORMAT_JSON,
                    'data' => [
                        'code' => $code,
                        'message' => !empty($message) ? $message : '',
//                        'data' => $data
                    ],
        ]);
    }
    /**
     * 
     * @param type $data
     * @param type $response_fields
     * @return type
     */
     public static function apiResponseFeildsArr($data,$response_fields=array()){
         $data_return=array();
         $return_key=0;
         foreach($data as $key_r=>$row){            
            if(isset($row['growpoint_external_id']) ){
                $data_return[$return_key]['external_id']=$row['external_id']= $row['growpoint_external_id'];
            }
            foreach($row as $key_f=>$field){        
                if(in_array($key_f,$response_fields)){
                    if(empty($field)){
                        $data_return[$return_key][$key_f]='';
                    }else{
                        $data_return[$return_key][$key_f]= $field;                    
                    }
                }
            }
            $return_key++;
        }
        return $data_return;
     }
    
    

}
