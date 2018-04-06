<?php
/**
 * Created by PhpStorm.
 * User: I073349
 * Date: 4/6/2018
 * Time: 11:27 AM
 */

namespace HXS\ZQ;

class ZhongQian
{

    /**
     * 用户真实性验证
     * @param $realUserName
     * @param $userCardId
     * @return bool
     */
    public function verifyUserId($realUserName, $userCardId)
    {
        $url = 'http://'.config('zhongqian.zq_domain').'/a2e';
        $postData = array(
            "zqid" => '',
            "name" => $realUserName,
            "idcard" => $userCardId
        );
        $result = Helper::curlPost($url, $postData);
        // TODO 需要验证返回结果
        return true;
    }

    public function test()
    {
        echo config('zhongqian.zqid');
    }

    public function pushUserToZhongQian($userId, $name, $phone, $cardId)
    {
        $arr = array(
            "user_name" => $name,
            "name" => $name,
            "mobile" => $phone,   //联系人电话
            "user_type" => 1,   //用户类型  0企业  1个人
            "zqid" => config('zhongqian.zqid'),  //众签唯一标示
            "user_code" => $userId,  //用户唯一标示
            "id_card_no" => $cardId,//身份证号码
            "notify_url" => config('zhongqian.push_user_notify_callback')   //异步回调
//            "return_url" => $return_url   //同步可省略
        );
        $sign_val = Helper::zqSign($arr, config('zhongqian.private_key'));
        $arr['sign_val'] = $sign_val;
        //得到结果
        $url = 'http://'.config('zhongqian.zq_domain').'/personReg';
        $content = Helper::curlPost($url, $arr);
        return $content;
    }

    public function createContractByTempateId($templateId, $userCode, $json='')
    {
        //合同编号
        $contract_num = Helper::generateSN();
        //合同名称
        $contract_name = config('zhongqian.zq_contract_name');
        $json_val = '{"jsonVal":[{"order_sn":"","first_part":"","second_part":"","id_number":"","second_part_phone":"","second_part_address":"","loan_date":"","borrow_amount":"","borrow_days":"","borrow_rate":"","repay_date":"","total_amount":"","Signer1":"","Signer2":"","Signer3":""}]}';
        $json_val = '{"jsonVal":[]}';
        $arr=array(
            "zqid"  => config('zhongqian.zqid'),  //众签唯一标示
            't_no' => $templateId,
            'no' => $contract_num,
            'name' => $contract_name,
            'contract_val' => $json_val,
        );
        $sign_val = Helper::zqSign($arr, config('zhongqian.private_key'));
        $arr['sign_val'] = $sign_val;
        $url = 'http://'.config('zhongqian.zq_domain').'/pdfTemplate';
        $content = Helper::curlPost($url, $arr);
        return $content;
    }
}