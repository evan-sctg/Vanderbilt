<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\GitHubSearchResponse;


class ProjectsController extends Controller
{
    //



	/**
	* front end page for viewing individual project details in list view
	*/
        public function ViewProjectDetails(Request $request){ 
	
	$projectID = $request->route('projectid');   
	$projectQry=\App\Project::where('ID',$projectID)
	->select( 
	'projects.Name',
	'projects.RepoID',
	'projects.URL',
	'projects.Description',
	'projects.Stars',
	'projects.Forks',
	'projects.Issues',
	'projects.OwnerID',
	'projects.OwnerName',
	'projects.OwnerURL',
	'projects.OwnerAvatar',
	'projects.CreatedDate',
	'projects.PushedDate'
	);
	
	//verify project exist
	if($projectQry->count()>0){
	$project=$projectQry->first()->ToArray();//fetch record and format as an associative array
	return view('details')->with(['project' => $project]);//pass record to details page for output
	}else{
	return view('invalid');	
	}
	
	
		
	}	




	/**
	* endpoint for frontend list view search ajax requests - formatted for jquery datatables
	*/
        public function ViewProjects_datatable(Request $request){ 
	
	
		//setup default ordering
		if(!isset($request->order) ){ 
		    $Orderby='Stars';
		    $OrderDir='DESC';
		}else{
		    $colNames=['Name','OwnerName','Stars','Forks','Issues','CreatedDate','PushedDate'];//used to map column names to datatable indexes
		    $orderbyIndex=$request->input('order.0.column');//the datatable column to sort by
		    $Orderby=(($orderbyIndex<7)? $colNames[$orderbyIndex]: 'Stars' );//if not valid column id fall back to stars
		    $OrderDir=$request->input('order.0.dir');//asc or desc
		}
	
		if(!isset($request->search) ){ 
		    $searchTerm="";
		}else{
		    $searchTerm=$request->input('search.value');//the datatable passed search text	
		}
		if(!isset($request->draw) ){	$request->draw=0;	}//used by datatables for draw ordering
		if(!isset($request->start) ){	$request->start=0;	}//indicates where the page starts
		if(!isset($request->length) ){	$request->length=10;	}//indicates where the page ends
		
		//build search query and filter results
		$datatableResults=\App\Project::where('Name', 'LIKE', '%'.$searchTerm.'%')
		->orWhere('OwnerName', 'LIKE', '%'.$searchTerm.'%')
		->orWhere('Description', 'LIKE', '%'.$searchTerm.'%')
		->orderBy($Orderby, $OrderDir)
		->select( 
		'projects.ID',
		'projects.Name',
		'projects.OwnerName',
		'projects.Stars',
		'projects.Forks',
		'projects.Issues',
		'projects.CreatedDate',
		'projects.PushedDate'
		);
		
		//count the total number of results after filtering the results
		$recordsFiltered = $datatableResults->count();
		
		//limit data to our paged data
		$pagedData=$datatableResults->skip( $request->start )->take( $request->length )->get();
		
		
		//respond with a json object
		return response()->json([
			'draw' => $this->sanitize_int($request->draw),//pass back the draw order variable passed by the datatable
			'recordsTotal' => \App\Project::count(),//the total number of records
			'recordsFiltered' => $recordsFiltered,//the total number of results after filtering the results
			'data' => $pagedData// the data for jsut the requested page
			]);
	
	
	}	

	/**
	* endpoint for frontend grid view search ajax requests
	*/
        public function ViewProjects(Request $request){ 
		if( isset($request->searchTerm) && !empty($request->searchTerm) ){
		    //return 9 prejects that contain the search term ordered by amount of stars descending //display first page of results if page not set
		    //Laravels ORM Eloquent uses PDO prepared statements to protect against SQL injection 
		    return \App\Project::where('Name', 'LIKE', '%'.$request->searchTerm.'%')
		    ->orWhere('OwnerName', 'LIKE', '%'.$request->searchTerm.'%')
		    ->orWhere('Description', 'LIKE', '%'.$request->searchTerm.'%')
		    ->orderBy('Stars', 'DESC')
		    ->paginate(9);
		}else{		
		    //return 9 prejects ordered by amount of stars descending //display first page of results if page not set	
		    return \App\Project::orderBy('Stars', 'DESC')->paginate(9);
		}
	}	


	/**
	* endpoint for frontend Scan ajax requests
	*/
        public function Scan(Request $request){ 
		//initiate variable defaults
		$maxRateLimit=30;
		$remainingRateLimit=0;
		$rateLimitSeconds=60;
		$errorEncountered=false;
		$statusCode=false;
		
		
		//GitHub Search API provides up to 1,000 results for each search. I decided to filter results by date to ensure we stay under the limit				
		$result=$this->GitHubSearchAPI($request->searchYear,$request->searchMonth,$request->searchDay,$request->minStars);//set criteria and make request
		if($result->StatusCode==200){//verify request was successful
		$this->ProccessSearchResponse($result);//Validate response and itterate through items		
		$maxRateLimit=$result->RateLimit;//set max rate limit
		$remainingRateLimit=$result->RemainingRateLimit;//set last received rate limit
		$rateLimitSeconds=$result->RateLimitSeconds();//set last received rate limit period end
		
		//start of handling of multiple pages of results
			if( isset($result->Paging) && isset($result->Paging['next']) && isset($result->Paging['last']) ){//if there is more than one page of results
			
				$pagesRemaining=$result->Paging['last']-$result->Paging['next']+1;//how many more pages of results
				if($result->RemainingRateLimit>=$pagesRemaining){//verify we can make the remaining requests and stay under our rate limit
					for($x=$result->Paging['next'];$x<=$result->Paging['last'];$x++){//itterate through remaining pages of results
						$pagedResult=$this->GitHubSearchAPI($request->searchYear,$request->searchMonth,$request->searchDay,$request->minStars,$x);//Use the GitHub API to retrieve a list of projects
						if($pagedResult->StatusCode==200){//verify request was successful
						    $this->ProccessSearchResponse($pagedResult);//Validate response and itterate through items		
						    $remainingRateLimit=$result->RemainingRateLimit;//set last received rate limit
						    $rateLimitSeconds=$result->RateLimitSeconds();//set last received rate limit period end
						}else{		
						    $errorEncountered=$pagedResult->StatusCode;
						    break;//stop looping through pages					
						}
					
					}//end for
				}
			
			}
		//end of handling of multiple pages of results		
			
		}else{		
		$errorEncountered=$result->StatusCode;		
		}
		
		
		//output response in json with details about rate limiting
		return response()->json([
			'maxRateLimit' => $maxRateLimit,
			'remainingRateLimit' => $remainingRateLimit,
			'remainingRateLimitSeconds' => $rateLimitSeconds,
			'status' => (($errorEncountered)? $errorEncountered:'OK' )
			]);
		
	
			
	
        }   
     

     
     
     
	
	/**
	* Validate response and itterate through items
	*/
        public function ProccessSearchResponse($searchResponse){ 
		if(isset($searchResponse->json['total_count']) && !empty($searchResponse->json['total_count'])){//verify number ofresults returned
			if(isset($searchResponse->json['items']) && !empty($searchResponse->json['items'])){//verify results items are set
				foreach($searchResponse->json['items'] as $searchResultItem){//itterate through each search response item
				    $this->StoreRecord($searchResultItem);//store search result item
				}
			}
		}
        }
     

	/**
	* Create or update search result item - sanitize data and create our sqll query
	*/
        public function StoreRecord($searchResultItem){ 
		if( isset($searchResultItem) && !empty($searchResultItem) ){//verify search result item is not empty
		
		$RepoObj = \App\Project::updateOrCreate(//Laravels ORM Eloquent uses PDO prepared statements to protect against SQL injection 
		    ['RepoID' => $this->sanitize_int($searchResultItem['id'])],
		    ['OwnerID' => $this->sanitize_int($searchResultItem['owner']['id']),
		    'OwnerName' => $this->sanitize_alphanumeric_string($searchResultItem['owner']['login']),
		    'OwnerURL' => $this->sanitize_repo_url($searchResultItem['owner']['html_url']),
		    'OwnerAvatar' => $this->sanitize_repo_url($searchResultItem['owner']['avatar_url']),
		    'RepoID' => $this->sanitize_int($searchResultItem['id']),
		    'Name' => $this->sanitize_alphanumeric_string($searchResultItem['name']),
		    'URL' => $this->sanitize_repo_url($searchResultItem['html_url']),
		    'Description' => $searchResultItem['description'],//the front end is cast as string by vue.js and never executed by browser &amp; would be not be turned into &
		    'Stars' => $this->sanitize_int($searchResultItem['stargazers_count']),
		    'Forks' => $this->sanitize_int($searchResultItem['forks']),
		    'Issues' => $this->sanitize_int($searchResultItem['open_issues']),
		    'CreatedDate' => $this->sanitize_date($searchResultItem['created_at']),
		    'PushedDate' => $this->sanitize_date($searchResultItem['pushed_at'])
		    ]
		);
		
		}	
	
        }
     
     

	/**
	* Use the GitHub API to retrieve a list of popular PHP projects
	*/
        public function GitHubSearchAPI($year,$month,$day,$minStars=100,$page=1){ 
	//GitHub Search API provides up to 1,000 results for each search. I decided to filter results by date to ensure we stay under the limit	
	
	//API URL
	$Endpoint = "https://api.github.com/search/repositories";
	
	//search and ordering parameters
	$Params=[
	'q' => 'created:'.$year.'-'.$month.'-'.$day.' stars:>='.$minStars.' language:php', //search for minStars or more stars in the language php on the specified day
	'sort' => 'stars',//order by how many stars
	'order' => 'desc',//order from greatest to least
	'per_page' => '100',//show 100 results per page
	'page' => $page//define what page of results to show
	];
		
	//start a new http client
	$Http = new \GuzzleHttp\Client();
	
	//run http request and store response object	
	$response = $this->DoAPIRequest($Http,$Endpoint,$Params);
	
	//construct search response object
	$Result=new GitHubSearchResponse($response);
	
	return $Result;
     }

	/**
	* Preform GitHub API request with retry option
	*/
        public function DoAPIRequest($Http,$Endpoint,$Params,$attempt=1){
	$maxRetry=1;
	
	//run http request and store response object
	try {
	$response = $Http->request('GET', $Endpoint, [
	'headers' => //set request headers
	[
	'Authorization'=>'token 5fae7e3a9c9f23d3ba641ddc6612fcd7d44957f1',//pass authentication token for increased rate limit
	'Accept'=>'application/vnd.github.v3+json'//define that we want to use version 3 of the api
	],
	'query' => $Params]);//set query parameters
	} catch (\GuzzleHttp\Exception\ServerException $e) {//catch 500 errors
		$errorCode=$e->getCode();
		if($attempt<=$maxRetry && $errorCode == 502){//on bad gateway retry if we are under our retry limit
		   return $this->DoAPIRequest($Http,$Endpoint,$Params,++$attempt);
		}else{
		   return $errorCode;
		}
    
	} catch (\GuzzleHttp\Exception\ClientException $e) {	//catch 400 errors
	   return $e->getCode();
	}
	
		
	
	
	return $response;
     }
     
     
     
	
	
	
	
	
	
	/**
	* sanitize date - only allow numeric , dash, colon, space
	*/
	function sanitize_date($string)
	{
	  $string = preg_replace("/[^-0-9: ]/", "", str_replace(['T','Z'],[' ',''],$string));
	  return $string;
	}

	
	/**
	* sanitize repo url strict - only allow alphanumeric , colon, forward slash, period, question mark, and equal sign
	*/
	function sanitize_repo_url($string)
	{
	  $string = preg_replace("/[^-a-zA-Z0-9:\/.?=]/", "", $string);
	  return $string;
	}
	
		
	
	/**
	* OWASP sanitize for html - sanitize a string for HTML
	*/
	function sanitize_html_string($string)
	{
	  $pattern[0] = '/\&/';
	  $pattern[1] = '/</';
	  $pattern[2] = "/>/";
	  $pattern[3] = '/\n/';
	  $pattern[4] = '/"/';
	  $pattern[5] = "/'/";
	  $pattern[6] = "/%/";
	  $pattern[7] = '/\(/';
	  $pattern[8] = '/\)/';
	  $pattern[9] = '/\+/';
	  $pattern[10] = '/-/';
	  $replacement[0] = '&amp;';
	  $replacement[1] = '&lt;';
	  $replacement[2] = '&gt;';
	  $replacement[3] = '<br>';
	  $replacement[4] = '&quot;';
	  $replacement[5] = '&#39;';
	  $replacement[6] = '&#37;';
	  $replacement[7] = '&#40;';
	  $replacement[8] = '&#41;';
	  $replacement[9] = '&#43;';
	  $replacement[10] = '&#45;';
	  return preg_replace($pattern, $replacement, $string);
	}

	/**
	* OWASP PHP Filters sanitize string - only allow alphanumeric
	*/
	function sanitize_alphanumeric_string($string)
	{
	  $string = preg_replace("/[^a-zA-Z0-9]/", "", $string);
	  return $string;
	}


	/**
	* OWASP PHP Filters sanitize integer - only allow integer value
	*/
	function sanitize_int($integer)
	{
	  $int = intval($integer);
	  return $int;
	}


	/**
	* OWASP PHP Filters sanitize float - only allow float value
	*/
	function sanitize_float($float)
	{
	  $float = floatval($float);
	  return $float;
	}	
	
 
     
     
     

}
