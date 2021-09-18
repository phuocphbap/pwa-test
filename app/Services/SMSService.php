<?php

namespace App\Services;

use AWS;
use Aws\Exception\AwsException;

class SMSService
{
    protected $sms;

    public function __construct()
    {
        $this->sms = AWS::createClient('sns');
    }

    public function sendSMS($phoneNumber, $message)
    {
        try {
            $data = $this->sms->publish([
                'Message' => $message,
                'PhoneNumber' => $phoneNumber,
                'MessageAttributes' => [
                    'AWS.SNS.SMS.SMSType' => [
                        'DataType' => 'String',
                        'StringValue' => 'Transactional',
                    ],
               ],
            ]);

            return true;
        } catch (AwsException $e) {
            error_log($e->getMessage());
        }
    }
}
