<?php
use GuzzleHttp\Client;
use Zarinpal\Drivers\RestDriver;
use Zarinpal\Zarinpal;

class RestTestCase extends PHPUnit_Framework_TestCase
{
    private $zarinpal;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $restDriver = new RestDriver();
        $restDriver->setAddress('https://sandbox.zarinpal.com/pg/rest/WebGate/');
        $this->zarinpal = new Zarinpal('XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX', $restDriver);

        parent::__construct($name, $data, $dataName);
    }

    public function testCorrectRequest()
    {
        $answer = $this->zarinpal->request("http://www.example.com/testVerify.php", 1000, 'testing');
        $this->assertEquals(strlen($answer['Authority']), 36);

        // try and mock pay form
        try{
            $client = new Client();
            $response = $client->request(
                'POST',
                'https://sandbox.zarinpal.com/pg/transaction/pay/' . $answer['Authority'],
                [
                    'form_params' => [
                        'ok' => 'ok',
                    ]
                ]);
        }catch (Exception $e){}

        $answer = $this->zarinpal->verify('OK', 1000, $answer['Authority']);
        $this->assertEquals($answer['Status'], 'success');
        $this->assertEquals(strlen($answer['Status']), 7);
    }
}