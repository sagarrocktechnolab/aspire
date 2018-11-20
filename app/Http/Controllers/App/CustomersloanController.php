<?php
/*
Project Name: RockTEchnolabs
Project URI: http://RockTechnolabs.com
Author: RockTEchnolabs Team
Author URI: http://RockTEchnolabs.com/
Version: 2.1
*/
namespace App\Http\Controllers\App;

//validator is builtin class in laravel
use Validator;

use Mail;
use DB;
//for password encryption or hash protected
use Hash;

//for authenitcate login data
use Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;

//for requesting a value 
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

//for Carbon a value 
use Carbon;

class CustomersloanController extends Controller
{
	
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   /* public function __construct()
    {
        $this->middleware('auth');
    }*/

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
		
		
	//Apply For Loan 
	public function applyForLoan(Request $request){
		$customer_id 							=   $request->customer_id;
		$loan_title 							=   $request->loan_title;
		$loan_description						=   $request->loan_description;
		$loan_duration							=   $request->loan_duration;
		$loan_percentage            			=   $request->loan_percentage;
		$loan_arrangement_fee           		=   $request->loan_arrangement_fee;			
		$loan_taken_date           				=   date('y-m-d');			
		$loan_amount    		   				=   $request->loan_amount;
		
		
		
		$customers_info_date_account_created 	=   date('y-m-d h:i:s');	
		
		$rules['customer_id'] = 'required|string';
		$rules['loan_title'] = 'required|string';		
		$rules['loan_duration'] = 'required|integer|min:1';		
		$rules['loan_percentage'] = 'required|numeric|min:1|max:100';		
		$rules['loan_arrangement_fee'] = 'required|min:1';		
		$rules['loan_amount'] = 'required|min:1';		

		$validator = Validator::make($request->all(), $rules);

		//Now check validation:
		if ($validator->fails()) 
		{ 
		  /* do something */ 
		  //response if email already exit	
		  $messages  = $validator->errors()->all();
			
		  $message  = '';
		  foreach($messages as $key => $errorRow){
		  	 $message .= $errorRow;
		  }
		  
			$postData = array();
			$responseData = array('success'=>'0', 'data'=>array(), 'message'=>array('error_message'=>$message));
			$userResponse = json_encode($responseData);
			print $userResponse;die;
		}
		
		if($loan_amount <= 0 || $loan_percentage <= 0 || $loan_title == '' || $loan_duration <= 0 || $customer_id <= 0 || $customer_id == ''  ){
			//response if customer not exit	
			$postData = array();
			$responseData = array('success'=>'0', 'data'=>$postData, 'message'=>"Add all required data. Loan amount,percentage and duration must be grater then zero.");
			$userResponse = json_encode($responseData);
			print $userResponse;
			die;
		}else{
			//check customer existance
			$existUser = DB::table('customers')->where('customers_id', $customer_id)->get();	
		}
		if(count($existUser) != "1"){	
			//response if customer not exit	
			$postData = array();
			$responseData = array('success'=>'0', 'data'=>$postData, 'message'=>"Customer is not exist with this id.");
			$userResponse = json_encode($responseData);
			print $userResponse;die;
		}else{
			$loan_interest_amount =  ($loan_amount * $loan_percentage * $loan_duration)/(100*12);
			$loan_repayment_amount =  $loan_amount + $loan_arrangement_fee + $loan_interest_amount;
			$customer_data = array(
				'customer_id'			 =>  $customer_id,
				'loan_code'				 =>  'LOAN_A_'.$customer_id.'_'.time(),
				'loan_title'			 =>  $loan_title,
				'loan_description'		 =>  $loan_description,
				'loan_duration'			 =>  $loan_duration,
				'loan_percentage'		 =>  $loan_percentage,
				'loan_arrangement_fee'	 =>  $loan_arrangement_fee,
				'loan_taken_date'		 =>  $loan_taken_date,
				'loan_amount'			 =>  $loan_amount,	
				'loan_interest_amount'	 =>  $loan_interest_amount,
				'loan_repayment_amount' =>  $loan_repayment_amount,
				'loan_status'			 =>  'Approved',//Pending,Approved,Repaid,Repayment
				'created_at'			 =>	 time()
			);
							
			//insert data into customer
			$loan_id = DB::table('customer_loans')->insertGetId($customer_data);
			
			$appliedLoanData = DB::table('customer_loans')->where('loan_id', '=', $loan_id)->get();

			$userData = DB::table('customer_loans')->where('customer_id', '=', $customer_id)->get();
			

			$responseData = array('success'=>'1','current_loan_data'=>$appliedLoanData, 'data'=>$userData, 'message'=>"Apply for loan successfully!");
			$userResponse = json_encode($responseData);
			print $userResponse;
			
			/*
			Mail::send('/mail/applyforload', ['userData' => $userData], function($m) use ($userData){
				$m->to($userData[0]->customers_email_address)->subject('Welcome to Aspire App"')->getSwiftMessage()
				->getHeaders()
				->addTextHeader('x-mailgun-native-send', 'true');	
			});*/
		}	
	}
	
	//Applied Loan Repayment
	public function loanRepay(Request $request){
		$customer_id 		=   $request->customer_id;
		$loan_code 			=   $request->loan_code;		
		$loan_repayamount   =   $request->loan_repayamount;
				
		$rules['customer_id'] = 'required|string';
		$rules['loan_code'] = 'required|string';		
		$rules['loan_repayamount'] = 'required|min:1';		
		

		$validator = Validator::make($request->all(), $rules);

		//Now check validation:
		if ($validator->fails()) 
		{ 
		  /* do something */ 
		  //response if email already exit	
		  $messages  = $validator->errors()->all();
			
		  $message  = '';
		  foreach($messages as $key => $errorRow){
		  	 $message .= $errorRow;
		  }
		  
			$postData = array();
			$responseData = array('success'=>'0', 'data'=>array(), 'message'=>array('error_message'=>$message));
			$userResponse = json_encode($responseData);
			print $userResponse;die;
		}
		

		if($loan_code == '' || $loan_repayamount <= 0 || $customer_id <= 0 || $customer_id == ''  ){
			//response if customer not exit	
			$postData = array();
			$responseData = array('success'=>'0', 'data'=>$postData, 'message'=>"Add all required data. Loan repayAmount must be grater then zero.");
			$userResponse = json_encode($responseData);
			print $userResponse;
			die;
		}else{
			//check customer existance
			$existUser = DB::table('customer_loans')->where('customer_id', $customer_id)->where('loan_code', $loan_code)->get()->toArray();	
			$existUserLoanRepayment = DB::table('customer_loan_repayment')->selectRaw('sum(customer_loan_repay_amount) as totalLoanPaid')->where('customer_id', $customer_id)->where('customer_loan_code', $loan_code)->get()->toArray();	
			
		}
		if(count($existUser) != "1"){	
			//response if customer not exit	
			$postData = array();
			$responseData = array('success'=>'0', 'data'=>$postData, 'message'=>"Customer loan detail is not exist with this customer and loan id.");
			$userResponse = json_encode($responseData);
			print $userResponse;die;
		}elseif($existUserLoanRepayment[0]->totalLoanPaid  > 0 && $existUser[0]->loan_repayment_amount <= $existUserLoanRepayment[0]->totalLoanPaid ){			
			
					//response if customer not exit	
				$postData = array();
				$responseData = array('success'=>'0', 'data'=>$postData, 'message'=>"Loan is already paid.");
				$userResponse = json_encode($responseData);
				print $userResponse;die;
			

		}else{
			$remainAmount  =   $existUser[0]->loan_repayment_amount - $existUserLoanRepayment[0]->totalLoanPaid;
			if($remainAmount < $loan_repayamount){
				$postData = array('remain_amount'=>$remainAmount);
				$responseData = array('success'=>'0', 'data'=>$postData, 'message'=>"Loan payment amount is grater then to paid.Your remain amount is ".$remainAmount);
				$userResponse = json_encode($responseData);
				print $userResponse;die;
			}else{
					$customer_data = array(
						'customer_id'			 			 =>  $customer_id,
						'customer_loan_id'			 		 =>  $existUser[0]->loan_id,
						'customer_loan_code'				 =>  $loan_code,				
						'customer_loan_repay_amount'		 =>  $loan_repayamount,	
						'created_at'			 			 =>	 time()
					);
									
					//insert data into customer
					$loan_id = DB::table('customer_loan_repayment')->insertGetId($customer_data);
					
					$userPaidLoanData = DB::table('customer_loan_repayment')->where('customer_id', '=', $customer_id)->where('customer_loan_code', '=', $loan_code)->get();
					$userData = DB::table('customer_loans')->where('customer_id', '=', $customer_id)->where('loan_code', '=', $loan_code)->get();
					

					$responseData = array('success'=>'1', 'paid_installment_loan_data' => $userPaidLoanData,'data'=>$userData, 'message'=>"Loan repay successfully!");
					$userResponse = json_encode($responseData);
					print $userResponse;
					
					
					/*Mail::send('/mail/loanrepay', ['userData' => $userData], function($m) use ($userData){
						$m->to($userData[0]->customers_email_address)->subject('Welcome to Aspire App"')->getSwiftMessage()
						->getHeaders()
						->addTextHeader('x-mailgun-native-send', 'true');	
					});*/
				}		
		}	
	}
	
	//notify me
	public function notify_me(Request $request){
		
		$device_id 			=  $request->device_id;
		$is_notify 			=  $request->is_notify;
		
		$devices = DB::table('devices')->where('device_id', $device_id)->get();
		if(!empty($devices[0]->customers_id)){
		$customers = DB::table('customers')->where('customers_id', $devices[0]->customers_id)->get();	
		
		if(count($customers>0)){
		
			foreach($customers as $customers_data){
				
				DB::table('devices')->where('customers_id', $customers_data->customers_id)->update([
					'is_notify'   =>   $is_notify,
					]);	
			}
			
		}
		}else{
			
			DB::table('devices')->where('device_id', $device_id)->update([
					'is_notify'   =>   $is_notify,
					]);	
		}
		
		$responseData = array('success'=>'1', 'data'=>'',  'message'=>"Notification setting has been changed successfully!");
		$categoryResponse = json_encode($responseData);
		print $categoryResponse;
	}
	
	
	
	
	
		
	//generate random password
	function createRandomPassword() { 
		$pass = substr(md5(uniqid(mt_rand(), true)) , 0, 8);	
		return $pass; 
	} 
	
	
	
	
	
}