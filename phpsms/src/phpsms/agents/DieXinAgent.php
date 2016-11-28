<?php

namespace Toplan\PhpSms;

/**
 * Class DinXinAgent
 *
 * @property string $smsUser
 * @property string $smsKey
 */
class DieXinAgent extends Agent
{
    public function sendSms($to, $content, $tempId, array $data)
    {

        $this->sendContentSms($to, $content);
    }

    public function sendContentSms($to, $content)
    {
        $url = "http://sms.airlead.net:28080/HIF12/mt";
        $optData = json_encode (array(
            'Userid'     => $this->smsUser,
            'Passwd'     => $this->smsKey,
            'Cli_Msg_Id' => uniqid(),
            'Mobile'     => $to,
            'Content'    => $content
        ));
        $data = $this->sendCurl($url, $optData);
        $this->setResult($data);
    }

    public function voiceVerify($to, $code, $tempId, array $data)
    {

    }

    protected function sendCurl($url, $optData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $optData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($optData)
            )
        );
        ob_start();
        curl_exec($ch);
        $result = ob_get_contents();
        ob_end_clean();
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        dd($return_code);
        curl_close($ch);
        return $return_code;
    }

    protected function setResult($result)
    {
        $this->result(Agent::INFO, $result);
        $result = json_decode($result, true);
        $this->result(Agent::SUCCESS, $result['error'] === 0);
        $this->result(Agent::CODE, $result['error']);
    }

    public function sendTemplateSms($to, $tempId, array $data)
    {
    }
}
