<?php
namespace App\Services;

use Exception;
use SoapClient;

class BankID
{

    const INVALID_PARAMETERS      = "INVALID_PARAMETERS ";
    const ALREADY_IN_PROGRESS     = "ALREADY_IN_PROGRESS ";
    const INTERNAL_ERROR          = "INTERNAL_ERROR";
    const OUTSTANDING_TRANSACTION = "OUTSTANDING_TRANSACTION";
    const NO_CLIENT               = "NO_CLIENT";
    const STARTED                 = "STARTED";
    const USER_SIGN               = "USER_SIGN";
    const COMPLETE                = "COMPLETE";
    const USER_CANCEL             = "USER_CANCEL";

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
}
