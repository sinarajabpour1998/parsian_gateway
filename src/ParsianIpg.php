<?php

namespace Sinarajabpour1998\ParsianGateway;

class ParsianIpg{
	
	public function __construct($args=[])
	{
		$this->pin = isset( $args['pin'] ) ? $args['pin'] : '';
	}
    
    public function paymentRequest($amount, $order_id, $callback_url)
    {
		$args = [
			'LoginAccount'	=> $this->pin,
			'OrderId'		=> $order_id,
			'Amount'		=> $amount,
			'CallBackUrl'	=> $callback_url
		];
		$client	= new \SoapClient( 'https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?WSDL' );
		$result = $client->SalePaymentRequest( [ 'requestData' => $args ] );
			
		$response = new \stdClass();
		$status = $result->SalePaymentRequestResult->Status;
		$token = $result->SalePaymentRequestResult->Token;
		$message = $result->SalePaymentRequestResult->Message ?? NULL;
		
        if( $status == 0 && $token > 0 ){
            $response->status = 'success';
            $response->token = $token;
        }
        else{
            $response->status = 'error';
            $response->message = $message;
        }
        
        return $response;
	}
    
    public function confirmPayment($token)
    {
		$args = [
			'LoginAccount'	=> $this->pin,
			'Token'			=> $token,
		];
		$client = new \SoapClient( 'https://pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?WSDL' );
		$result = $client->ConfirmPayment( [ 'requestData' => $args ] );
		
		$response = new \stdClass();
		$status = $result->ConfirmPaymentResult->Status;
		$message = $result->ConfirmPaymentResult->Message ?? NULL;
		$card_no_masked = $result->ConfirmPaymentResult->CardNumberMasked ?? NULL;
		$rrn = $result->ConfirmPaymentResult->RRN;
		
		if( $status == 0 ){
            $response->status = 'success';
        }
        else{
            $response->status = 'error';
            $response->message = $message;
            $response->card_no_masked = $card_no_masked;
			$response->rrn = $rrn;
        }
        
        return $response;
	}
	
	public function reversalRequest($token)
	{
		$args = [
			'LoginAccount'	=> $this->pin,
			'Token'			=> $token,
		];
		$client = new \SoapClient( 'https://pec.shaparak.ir/NewIPGServices/Reverse/ReversalService.asmx?WSDL' );
		$result = $client->ReversalRequest( [ 'requestData' => $args ] );
		
		$response = new \stdClass();
		$status = $result->ReversalRequestResult->Status;
		$message = $result->ReversalRequestResult->Message ?? NULL;
		
		if( $status == 0 ){
            $response->status = 'success';
        }
        else{
            $response->status = 'error';
            $response->message = $message;
        }
        
        return $response;
	}
	
}
