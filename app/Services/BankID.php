<?php
namespace App\Services;

use App\Transformers\BankidTransformer;
use Exception;
use SoapClient;
use SoapFault;

class BankID
{

    const INVALID_PARAMETERS      = "INVALID_PARAMETERS";
    const ALREADY_IN_PROGRESS     = "ALREADY_IN_PROGRESS";
    const INTERNAL_ERROR          = "INTERNAL_ERROR";
    const OUTSTANDING_TRANSACTION = "OUTSTANDING_TRANSACTION";
    const NO_CLIENT               = "NO_CLIENT";
    const STARTED                 = "STARTED";
    const USER_SIGN               = "USER_SIGN";
    const COMPLETE                = "COMPLETE";
    const USER_CANCEL             = "USER_CANCEL";
    const CANCEL                  = "CANCELLED";
    const EXPIRED_TRANSACTION     = "EXPIRED_TRANSACTION";

    /**
     * @var mixed
     */
    protected $soapClient;

    /**
     * @var mixed
     */
    protected $wsdl;

    /**
     * @var mixed
     */
    protected $local_cert;

    /**
     * @var mixed
     */
    protected $ca_cert;

    /**
     * @var array
     */
    protected $context_options = [];

    /**
     * @var mixed
     */
    protected $bankidTransformer;
    /**
     * @var mixed
     */
    protected $ssl_context;

    public function __construct()
    {
        $this->wsdl = config('services.bankid.wsdl');

        $this->local_cert = base_path('storage/certs/certname.pem');

        $this->ca_cert = base_path('storage/certs/appapi.test.bankid.com.pem');

        $this->context_options['ssl'] = [
            'local_cert'          => $this->local_cert,
            'cafile'              => $this->ca_cert,
            'verify_peer'         => true,
            'verify_peer_name'    => true,
            'verify_depth'        => 5,
            'peer_name'           => config('services.bankid.peer_name'),
            'disable_compression' => true,
            'SNI_enabled'         => true,
            'ciphers'             => 'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4',

        ];

        $this->bankidTransformer = new BankidTransformer;

        $this->ssl_context = stream_context_create($this->context_options);

        if (!file_exists($this->ca_cert)) {
            throw new Exception('Unable to load server certificate: ' . $this->ca_cert, 2);
        }

        if (!file_exists($this->local_cert)) {
            throw new Exception('Unable to load client certificate: ' . $this->local_cert, 3);
        }

        if ($this->ssl_context === null) {
            throw new Exception('Failed to create a stram context for communication with server (' . config('services.bankid.peer_name') . ')', 1);
        }

        $this->soapClient = new SoapClient($this->wsdl, [
            'stream_context' => $this->ssl_context,
        ]);
    }

    /**
     * @return mixed
     */
    public function authenticate()
    {
        try {

            $args['personalNumber'] = cleanSSN(session('ssn'));

            $authResponse = $this->soapClient->Authenticate($args);

            if (!isset($authResponse->orderRef) || !isset($authResponse->autoStartToken)) {

                throw new Exception('Bad response from BANKID');
            }

            $response = $this->bankidTransformer->transformAuthentication($authResponse);

            return $response;

        } catch (Exception $e) {
            if ($e instanceof SoapFault) {

                return $e->getMessage();

            }
        }
    }

    /**
     * @return mixed
     */
    public function collectStatus()
    {
        $args = session('orderRef');

        try {

            $result = $this->soapClient->Collect($args);

            if (!isset($result->progressStatus)) {

                throw new Exception('Bad response from BankID');
            }

            $status = $this->bankidTransformer->transformCollect($result);

            return $status['status'];

        } catch (Exception $e) {

            return $e->getMessage();
        }
    }

    /**
     * @param $status
     */
    public function getMessage($status)
    {

        switch ($status) {
            case self::OUTSTANDING_TRANSACTION:
                $message = "Continue and open BankID app";
                break;

            case self::NO_CLIENT:
                $message = "Start BankID app";
                break;
            case self::STARTED:
                $message = "Searching for BankID, this may take a while";
                break;
            case self::USER_SIGN:
                $message = "Please enter your password on BankID";
                break;

            case self::USER_CANCEL:
                $message = "You have canceled the signin";
                break;

            case self::INVALID_PARAMETERS:
                $message = "Invalid parameters";
                break;

            case self::EXPIRED_TRANSACTION:
                $message = "Please try again, the first attemt expired!";
                break;

            case self::CANCEL:
                $message = "You are trying to send another order!";
                break;
            default:
                $message = "An error occured: " . $status;
                break;
        }

        return $message;
    }
}
