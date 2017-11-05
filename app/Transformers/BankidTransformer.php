<?php

namespace App\Transformers;

class BankidTransformer
{

    /**
     * @param $request
     */
    public function transformAuthentication($request)
    {

        return [

            'orderRef'       => $request->orderRef,
            'autoStartToken' => $request->autoStartToken,
        ];
    }

    /**
     * @param $request
     */
    public function transformCollect($request)
    {

        return [

            'status' => $request->progressStatus,
        ];
    }
}
