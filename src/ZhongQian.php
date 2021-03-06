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
//        $url = 'http://test.sign.zqsign.com/zqsign-web-identify/test/a2e';
        $url = config('zhongqian.zq_verify_user_url');
        $arr = array(
            "zqid" => config('zhongqian.zqid'),
            "name" => $realUserName,
            "order_no" => Helper::generateSN(),
            "idcard" => $userCardId
        );
        $sign_val = Helper::zqSign($arr, config('zhongqian.private_key'));
        $arr['sign_val'] = $sign_val;
        $result = Helper::curlPost($url, $arr);
        return json_decode($result);
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
        $url = 'http://' . config('zhongqian.zq_domain') . '/personReg';
        $content = Helper::curlPost($url, $arr);
        return json_decode($content);
    }

    public function pushCompanyToZhongQian($userCode, $contact, $name, $address, $mobile, $certificate)
    {
        $arr = array(
            "contact"=>$contact,
            "name"=>$name,
            "mobile"=>$mobile,   //联系人电话
            "user_type"=> 0,   //用户类型  0企业  1个人
            "zqid"=> config('zhongqian.zqid'),  //众签唯一标示
            "user_code"=>$userCode,  //用户唯一标示
            "certificate"=>$certificate,  //社会统一代码或营业执照号码
            "address"=>$address,
            "notify_url" => config('zhongqian.push_user_notify_callback')   //异步回调
//            "return_url" => $return_url   //同步可省略
        );
        $sign_val = Helper::zqSign($arr, config('zhongqian.private_key'));
        $arr['sign_val'] = $sign_val;
        //得到结果
        $url = 'http://' . config('zhongqian.zq_domain') . '/entpReg';
        $content = Helper::curlPost($url, $arr);
        return json_decode($content);
    }

    public function createContractByTempateId($templateId, $json_val = '{"jsonVal":[]}')
    {
        //合同编号
        $contract_num = Helper::generateSN();
        //合同名称
        $contract_name = config('zhongqian.zq_contract_name');
        $arr = array(
            "zqid" => config('zhongqian.zqid'),  //众签唯一标示
            't_no' => $templateId,
            'no' => $contract_num,
            'name' => $contract_name,
            'contract_val' => $json_val,
        );
        $sign_val = Helper::zqSign($arr, config('zhongqian.private_key'));
        $arr['sign_val'] = $sign_val;
        $url = 'http://' . config('zhongqian.zq_domain') . '/pdfTemplate';
        $content = Helper::curlPost($url, $arr);
        return array(
            "contract_no" => $contract_num,
            "create_status" => json_decode($content)
        );
    }

    public function autoSign($contract_num, $signer)
    {
        $url = 'http://' . config('zhongqian.zq_domain') . '/signAuto';
        $notify_url = config('zhongqian.auto_sign_notify_callback');
        $arr=array(
            "zqid"  => config('zhongqian.zqid'),  //众签唯一标示
            'no' => $contract_num,
            'signers' => $signer,
            "notify_url"=>$notify_url   //异步回调
        );
        //签字sign规则
        $ws_sign_val = Helper::zqSign($arr, config('zhongqian.private_key'));
        $arr['sign_val'] =  $ws_sign_val;
        $content = Helper::curlPost($url, $arr);
        return json_decode($content);
    }

    public function showSign($contractNo, $signer)
    {
        $url = 'http://' . config('zhongqian.zq_domain') . '/mobileSignView';
        $notify_url = config('zhongqian.show_sign_notify_callback');
        $return_url = config('zhongqian.show_sign_return_url');
        $SIGNATURECODE = "SIGNATURE";
        $arr = array(
            "zqid" => config('zhongqian.zqid'),  //众签唯一标示
            'no' => $contractNo,
            'user_code' => $signer,
            'sign_type' => $SIGNATURECODE,
            "notify_url" => $notify_url,   //异步回调
            "return_url" => $return_url, //同步回调
        );
        $sign_val = Helper::zqSign($arr, config('zhongqian.private_key'));
        $arr['sign_val'] = $sign_val;
        return array(
            "data" => $arr,
            "url" => $url
        );
    }

    /**
     * 返回代码为0表示成功
     * @param $userCode
     * @param $imgBase64
     * @return mixed
     */

    public function signatureChange($userCode, $imgBase64)
    {
        $url = 'http://' . config('zhongqian.zq_domain') . '/signatureChange';
//        $base64 = base64_encode(file_get_contents(public_path('img/test.jpg')));
        $arr=array(
            "zqid"  => config('zhongqian.zqid'),
            'user_code' => $userCode,
            'signature' => $imgBase64
        );
        //签字sign规则
        $ws_sign_val = Helper::zqSign($arr, config('zhongqian.private_key'));
        $arr['sign_val'] =  $ws_sign_val;
        $content = Helper::curlPost($url, $arr);
        return $content;
    }



    public function completionContract($contract_no)
    {
        $url = 'http://' . config('zhongqian.zq_domain') . '/completionContract';
        $notify_url = config('zhongqian.completion_contract_notify_callback');
        $arr=array(
            "zqid"  => config('zhongqian.zqid'),
            'no' => $contract_no,
            "notify_url"=>$notify_url   //异步回调
        );
        //签字sign规则
        $ws_sign_val = Helper::zqSign($arr, config('zhongqian.private_key'));
        $arr['sign_val'] =  $ws_sign_val;
        $content = Helper::curlPost($url, $arr);
        return $content;
    }

    public function downloadContractImage($contract_no)
    {
        $url = 'http://' . config('zhongqian.zq_domain') . '/getImg';
        $notify_url = config('download_contract_img_notify_callback');
        //组合接口需要的参数
        $arr=array(
            "zqid"  =>config('zhongqian.zqid'),
            'no' => $contract_no,
            "notify_url"=>$notify_url,   //异步回调
        );
        //签字sign规则
        $ws_sign_val = Helper::zqSign($arr, config('zhongqian.private_key'));
        $arr['sign_val'] =  $ws_sign_val;
        $content = Helper::curlPost($url, $arr);
        $result = json_decode($content);
        return $result;
    }
}