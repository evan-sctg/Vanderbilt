<?php
namespace App\Classes;

use GuzzleHttp\Psr7;

/**
* GitHub API Search Response
*/
class GitHubSearchResponse {
   public $StatusCode;//response status code
   public $Body;//response body
   public $json;//json decoded response
   public $Paging;//information about paging (next page number, last page number )
   public $RateLimit;//max requests per rate limit cycle
   public $RemainingRateLimit;//number of requests remaining before rate limit 
   public $RateLimitPeriodEnd;//rate limit period end time
   
   
/**
* setup Search Response object
*/
   function __construct($response) {
	$this->json=[];

	if(is_int($response)){//check if is error code
	$this->StatusCode = $response;
	}else{
	$this->StatusCode = $response->getStatusCode();//check request result
	$this->Body=$response->getBody()->getContents();
	if($this->StatusCode==200){//verify request successful
	$this->json=\GuzzleHttp\json_decode($this->Body, true);//decode json response
	$this->RateLimit=$response->getHeaderLine('X-RateLimit-Limit');//get max requests per rate limit cycle
	$this->RemainingRateLimit=$response->getHeaderLine('X-RateLimit-Remaining');//get number of requests remaining before rate limit 
	$this->RateLimitPeriodEnd=$response->getHeaderLine('X-RateLimit-Reset');//get epoch time of next rate limit period // how long until rate limit resets
	
	//parse the Link header value for information about paging (next page number, last page number )
	$parsed = Psr7\parse_header($response->getHeaderLine('Link'));
	$this->Paging=[];
	
		//reformat to usable format: Array(['next'] => 2,['last'] => 10)
		foreach($parsed as $curParse){
		$this->Paging[$curParse['rel']]=str_replace('&page=','',strstr(strstr($curParse[0], '&page='), '>',true));	
		}
	
	}
	}
   

	
	
   }
   
   
/**
* get seconds left in rate limit period
*/
	public function RateLimitSeconds(){	
	$CurTime = time();//get current time
	$RateLimitPeriod=$this->RateLimitPeriodEnd-$CurTime;//determine seconds left in rate limit period
	return (($RateLimitPeriod<0)? 0: $RateLimitPeriod);//return seconds remaining in rate limit period
	}  
	
}