<template>
	<div class="page-content">



	<!-- Error start -->
		<div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="errorMessage" >
		 {{ errorMessage }}
		  <button type="button" class="close" @click="errorMessage = null" aria-label="Close">
		    <span aria-hidden="true">×</span>
		  </button>
		</div>
	<!-- Error end -->


	<!-- Tab Navigation start -->
	<ul class="nav nav-tabs">
		<li class="active tab-GettingStarted"><a data-toggle="tab" href="#Tab_GettingStarted" @click="SetActiveTab('tab-GettingStarted');">Getting Started</a></li>
		<li class="tab-Scan"><a data-toggle="tab" href="#Tab_Scan" @click="SetActiveTab('tab-Scan');">Scan</a></li>
		<li class="tab-Browse"><a data-toggle="tab" href="#Tab_Browse" @click="SetActiveTab('tab-Browse');RefreshViewData();">Browse Projects</a></li>
	</ul>
	<!-- Tab Navigation end -->





	<!-- Tab Content start -->
	<div class="tab-content">   
    
    
    
    
    
    
	<!-- Tab GettingStarted start -->
	<div id="Tab_GettingStarted" class="tab-pane fade in active show">  
	<h3> Popular PHP Repositories on GitHub</h3>

	<b> Using PHP and MySQL, complete the following:</b>
	<br />
	
	<ol class="InstructionList">
	<li>
		<div class="">
			Use the GitHub API to retrieve the most starred public PHP projects. Store
			the list of repositories in a MySQL table. The table must contain the
			repository ID, name, URL, created date, last push date, description, and
			number of stars. This process should be able to be run repeatedly and
			update the table each time.
		</div> 
		<div class="stepResponse">
			I decided to retrieve the projects based on creation date to narrow results returned to stay under the GitHub 1000 result limit. I also decided to store information about the creator of the projects as well as how many forks and issues the project has.
			<div class="col-md-12">
				<a data-toggle="tab" href="#Tab_Scan" @click="SetActiveTab('tab-Scan');">Start New Scan</a>
			</div> 
		</div> 	
	</li>
	
	
	<li>	
		<div class="">
			Using the data in the table created in step 1, create an interface that
			displays a list of the GitHub repositories and allows the user to click
			through to view details on each one. Be sure to include all of the fields in
			step 1 – displayed in either the list or detailed view
		</div> 
		 
		<div class="stepResponse">
			I created a grid view and a list view with basic paging and search that includes information about the author as well. 
			<div class="col-md-12">
				<a data-toggle="tab" href="#Tab_Browse" @click="SetActiveTab('tab-Browse');ViewProjects();">Browse Projects</a>
			</div> 
		</div> 


	</li>
	
	
	
	<li>
	
		<div class="">
			Create a README file with a description of your architecture and notes on
			installation of your application. You are free to use any PHP, JavaScript, or
			CSS frameworks as you see fit.
		</div>  
		    
		<div class="stepResponse">
			<div>
				<p>I built a web application that's powered by Laravel and Vue.js 
				The main interface Allows you to scan for new projects using a date range selector and minimum star count.
				You can also Browse and search through previously scanned projects, both grid and list views are available.
				In grid view, you have the option to go to the repository page and in list view, you have the option to go into a detailed view.
				
				To protect against SQL injection it uses PDO prepared statements. 
				When creating interfaces I use either blade templates which escape template variables 
				by default or I use Vue.js components which cast its templates as strings and are never executed by the browser.
				All of the API endpoints and interfaces have CSFR token handling and validation which our ajax requests validate.
				
				
				</p>
				<p>Built using CentOS7, PHP7, MySQL/MarinaDB, Apache 2.4 , Laravel, Vue.js</p>
				<p>
				Laravel requirements:
				<ul>
					<li>PHP >= 7.1.3</li>
					<li>OpenSSL PHP Extension</li>
					<li>PDO PHP Extension</li>
					<li>Mbstring PHP Extension</li>
					<li>Tokenizer PHP Extension</li>
					<li>XML PHP Extension</li>
					<li>Ctype PHP Extension</li>
					<li>JSON PHP Extension</li>
					<li>BCMath PHP Extension</li>
				</ul>
				</p>

			</div>
			<ol class="col-md-12">			
				<li>install composer</li>
				<li>install node using node version manager</li>
				<li>Upload project folder "application" to server</li>
				<li>Edit "application/.env" file  (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_URL)</li>
				<li>Point apache to the "application/public" directory and set AllowOverride to allow htaccess rewites</li>
				<li>Navigate to the "application" directory and run the command "composer update" to regenerate vendor files</li>
				<li>In the "application" directory and run the command "npm install" to install node dependencies</li>
				<li>In the "application" directory and run the command "php artisan migrate" to build the database structure</li>
				<li>In the "application" directory and run the command "npm run dev" to build the application</li>
				<li>Navigate in your browser to the path that you previously pointed to "application/public" where you can now scan or browse projects</li>
			</ol>
				<p>
				Primary files:
				<ul>
					<li>/routes/web.php</li>
					<li>/app/Http/Controllers/ProjectsController.php</li>
					<li>/app/Classes/GitHubSearchResponse.php</li>
					<li>/resources/js/components/GitHubAPIInterface.vue</li>
					<li>/resources/views/details.blade.php</li>
					<li>/database/migrations/2019_2_2_000000_create_projects_table.php</li>
				</ul>
				</p>
		</div> 
	
	</li>
	
	</ol>
	
		



		

	</div>   
	<!-- Tab GettingStarted end --> 
    
    
    
    
    
    
	<!-- Tab Scan start -->
	<div id="Tab_Scan" class="tab-pane fade">
    
    
		<!-- ScanDetails start -->
		<div  class="ScanDetails">      

			<!--  Scan header start -->
			<div class=" row justify-content-center">
			       <div class="col-md-4">
				       <h3>Scan for new projects</h3>
				       <hr />
			       </div>
			</div> 
			<!-- Scan header end -->

			<!-- Progress bar start -->
			<div class="progress"  v-if="showProgress">      
				<div class="progress-bar" role="progressbar" v-bind:style="{ width: percentComplete + '%' }"  :aria-valuenow="{ percentComplete }" aria-valuemin="0" aria-valuemax="100">{{ percentComplete }}%</div>
			</div>
			<div v-if="showProgress" class=" row justify-content-center CenterText" >    
				<div class="col-md-6">Status: {{progressMessage}}</div>
			</div>
			<div v-if="showProgress" class=" row justify-content-center CenterText" >
				<div v-if="remainingTime" class="col-md-6">Time Remainging: {{remainingTime}}</div>
			</div>
			<!-- Progress bar end -->
			
			<!-- Scan settings start -->
			<div class="row justify-content-end">
				<div class="col-md-2 mb-3">
				      <input type="number" class="form-control" v-model.number="minStars" @change="NewSearch()"  min="0" max="50000" placeholder="Minimum stars" value=""  :disabled="showProgress">
				      <label>Minimum stars</label>
				</div>    
			</div>
		    
			<div class="row justify-content-center">
				<div class="col-md-6 mb-3">
				      <div id="dateRange"></div>
				      <label >Date Selection ( {{crawlStart}} - {{crawlEnd}} )</label>
				</div>    
			</div>
			<!-- Scan settings end -->
			
			
			<!-- Scan control start -->
			<div class="row justify-content-center CenterText">
				<div class="col-md-4 mb-3">
				       <button @click="CancelPopulate()" class="btn btn-success" style="padding:5px" :disabled="!showProgress">Cancel Scan</button>
				</div>
				    
				<div class="col-md-4 mb-3">
				       <button @click="IndexGitHub()" class="btn btn-success" style="padding:5px" :disabled="showProgress">Start New Scan</button>
				</div>        
			</div>
			<!-- Scan control end -->
	       
	       
		</div>
		<!-- ScanDetails end -->
       

	</div>
<!-- Tab Scan end -->


    
 
    
    
    
<!-- Tab Browse start -->
    <div id="Tab_Browse" class="tab-pane fade">
    
      <!-- List view start -->
	<div class=" ListView" v-show="!gridView">
	
	<!-- Grid / List toggle start -->
	<div class="row justify-content-end grid-list-toggle-cont">
		<button @click="GridView(true)" class="btn btn-info grid-list-toggle">
		<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="th" class="svg-inline--fa fa-th fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M149.333 56v80c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V56c0-13.255 10.745-24 24-24h101.333c13.255 0 24 10.745 24 24zm181.334 240v-80c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24h101.333c13.256 0 24.001-10.745 24.001-24zm32-240v80c0 13.255 10.745 24 24 24H488c13.255 0 24-10.745 24-24V56c0-13.255-10.745-24-24-24H386.667c-13.255 0-24 10.745-24 24zm-32 80V56c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24h101.333c13.256 0 24.001-10.745 24.001-24zm-205.334 56H24c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24h101.333c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24zM0 376v80c0 13.255 10.745 24 24 24h101.333c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H24c-13.255 0-24 10.745-24 24zm386.667-56H488c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H386.667c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24zm0 160H488c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H386.667c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24zM181.333 376v80c0 13.255 10.745 24 24 24h101.333c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24z"></path></svg>
		Grid View
		</button>
	</div>
	<!-- Grid / List toggle end -->
	
	
	<!--  List start -->
	<table id="ListView" class="table table-striped table-bordered" style="width: 100%;">
	<thead>
            <tr>
                <th>Project</th>
                <th>Creator</th>
                <th>Stars</th>
                <th>Forks</th>
                <th>Issues</th>
                <th>Created date</th>
                <th>Pushed date</th>
                <th>View Link</th>
            </tr>
	</thead>
	<tfoot>
            <tr>
                <th>Project</th>
                <th>Creator</th>
                <th>Stars</th>
                <th>Forks</th>
                <th>Issues</th>
                <th>Created date</th>
                <th>Pushed date</th>
                <th>View Link</th>
            </tr>
	</tfoot>
	</table>
	<!--  List end -->
	
	</div>
      <!-- List view end -->
	
    
      <!-- Grid view start -->
	<div class=" GridView"  v-show="gridView">
	
		<!-- Grid / List toggle start -->
		<div class="row justify-content-end grid-list-toggle-cont">
			<button @click="GridView(false)" class="btn btn-info grid-list-toggle">
			<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="th-list" class="svg-inline--fa fa-th-list fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M149.333 216v80c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-80c0-13.255 10.745-24 24-24h101.333c13.255 0 24 10.745 24 24zM0 376v80c0 13.255 10.745 24 24 24h101.333c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H24c-13.255 0-24 10.745-24 24zM125.333 32H24C10.745 32 0 42.745 0 56v80c0 13.255 10.745 24 24 24h101.333c13.255 0 24-10.745 24-24V56c0-13.255-10.745-24-24-24zm80 448H488c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24zm-24-424v80c0 13.255 10.745 24 24 24H488c13.255 0 24-10.745 24-24V56c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24zm24 264H488c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24z"></path></svg>
			List View
			</button>
		</div>       
		<!-- Grid / List toggle end -->
       
      
	      <!-- Result count and search start -->
	      <div class=" row justify-content-between">
		      <div class=" ">
			      <div class="ResultCount"  >
			       Results Found {{ totalResults }} <p v-if="totalPages > 1">( page {{ page }}/{{ totalPages }})</p>
			      </div>
			      <div   v-if="totalResults == 0">
				<a data-toggle="tab" href="#Tab_Scan" @click="SetActiveTab('tab-Scan');">Start New Scan</a>
			      </div>
		      </div>    

		      <div class=""><input v-model="searchTerm" v-on:input="NewSearch()" class="form-control form-control-sm" placeholder="Search..."></div>	      
	      </div>      
	      <!-- Result count and search end -->
      
		<div class="ResultsContainer row" style="">
		    
		      <!-- loop Items start -->
		    <div class="ItemContainer col-md-4 " v-for="item in items">
		    <div class="ItemBorder">
		    
		    <div class="col-md-12 ItemTitle">{{ item.Name }}</div>
		      <!-- ItemData start -->
		    <div class="col-md-6 itemAuthor">  <a :href="item.OwnerURL"  class="" target="AuthorViewWindow"><img :src="item.OwnerAvatar " />{{ item.OwnerName }}</a></div>
		       <div class="col-md-6 itemStats">
		       <p>({{ item.RepoID }})</p>
		       
		       <div class="col-md-12"><svg class="octicon octicon-star v-align-text-bottom" viewBox="0 0 14 16" version="1.1" width="14" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M14 6l-4.9-.64L7 1 4.9 5.36 0 6l3.6 3.26L2.67 14 7 11.67 11.33 14l-.93-4.74L14 6z"></path></svg> ({{ item.Stars }})</div>
		       <div class="col-md-12"><svg class="octicon octicon-repo-forked v-align-text-bottom" viewBox="0 0 10 16" version="1.1" width="10" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M8 1a1.993 1.993 0 0 0-1 3.72V6L5 8 3 6V4.72A1.993 1.993 0 0 0 2 1a1.993 1.993 0 0 0-1 3.72V6.5l3 3v1.78A1.993 1.993 0 0 0 5 15a1.993 1.993 0 0 0 1-3.72V9.5l3-3V4.72A1.993 1.993 0 0 0 8 1zM2 4.2C1.34 4.2.8 3.65.8 3c0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zm3 10c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zm3-10c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2z" ></path></svg> ({{ item.Forks }})</div>
		       <div class="col-md-12"><svg class="octicon octicon-issue-opened" viewBox="0 0 14 16" version="1.1" width="14" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M7 2.3c3.14 0 5.7 2.56 5.7 5.7s-2.56 5.7-5.7 5.7A5.71 5.71 0 0 1 1.3 8c0-3.14 2.56-5.7 5.7-5.7zM7 1C3.14 1 0 4.14 0 8s3.14 7 7 7 7-3.14 7-7-3.14-7-7-7zm1 3H6v5h2V4zm0 6H6v2h2v-2z"></path></svg> ({{ item.Issues }})</div>
			
			
			</div>
		       
		      <div class=" col-md-12">		     	      
		      <p class="ItemDescription">{{ item.Description }}</p>
		      <p class="ItemDate">Created: {{ item.CreatedDate }}</p>
		       <p class="ItemDate">Pushed: {{ item.PushedDate }}</p>
		       <a :href="item.URL"  class="btn btn-success projectView" target="ProjectViewWindow">View Project</a>	
		      </div>		      
		      <!-- ItemData end -->
		      
		    </div>		    
		    </div>
		      <!-- loop Items end -->
		    
		  </div>
		  
		      <!-- Paging start -->
		    <div class="paging row"  v-if="totalPages > 1" >
		    
		    <div class="col-md-4" >
		       <button v-if="showPrev"@click="PreviousPage()" class="btn" style="padding:5px"> << Previous</button>
		    </div>
		    
		    <div class="col-md-4" >
			    <div class="pagecount">( page {{ page }}/{{ totalPages }})</div>
		    </div>
		    
		    <div class="col-md-4" >
		       <button v-if="showNext"@click="NextPage()" class="btn" style="padding:5px"> Next >></button>
		    </div>
		    
		    </div>
		      <!-- Paging end -->
      
      
      
      
       </div> 
      <!-- Grid view end -->
      
    
  </div>
<!-- Tab Browse end -->
  
  
                    </div>
<!-- Tab Content end -->
		   
		
                          
		  
		    
    </div>
    
</template> 



<script>
    export default {
        mounted()
	{
	
		//get current year used to set max range in slider
		let thisYear=new Date().getFullYear();
		//create a date range slider
	      jQuery( "#dateRange" ).slider({
	      range: true,
	      min: 2010,
	      max: thisYear,
	      values: [ 2010, thisYear ],
	      slide: (function( event, ui ) {
	          this.crawlStart=ui.values[ 0 ];
	          this.crawlEnd=ui.values[ 1 ];
	      }).bind(this)
	      });
	      
	      jQuery(window).bind('resize', this.NormalizeHeights);//recalculate container heights when the window changes size
	      this.SetToken(document.head.querySelector('meta[name="csrf-token"]').content);//tell axios and ajax to use our csfr token
	      setInterval(this.NewToken,1800000);//grab new token every 30 minutes // valid token required to obtain a new token
	      
	      
	      
	      //setup jquery datatable as our front end and configure it for server side proccessing
	      this.listViewTable=jQuery('#ListView').DataTable( {
	      serverSide: true,
	      processing: true,
	          columns: [//map column data names from our api to columns
	          { "data": "Name" },
	          { "data": "OwnerName" },
	          { "data": "Stars" },
	          { "data": "Forks" },
	          { "data": "Issues" },
	          { "data": "CreatedDate" },
	          { "data": "PushedDate" },
		  { "data": null,//create a button linking to project details
		  orderable: false,
		  render: function ( data, type, row ) {
		  return '<a href="/project/' + parseInt(data.ID) + '" target="_projectDetails" class="btn btn-primary" >View Details</a>';
		  }
		  }
	          ],
	      ajax: {//configure API 
	          url: '/view-list',//endpoint
	          type: 'POST',//send post requests
	          dataType: 'json',//expect json responses from server
                    error: function(data){
                        console.log(data);
                    }
	      }
	      } );
	      
	      
	      
	    
        },
        data() 
	{
            return {
                items: [],//list of results for display in view tab
		errorMessage: "",//Error text for user
		progressMessage: "",//status text for user
		showProgress: false,//controls visibility of progress bar
		percentComplete: 0,//our current progress
		remainingTime: "",//text showing an estimate of the time remaining
		
		//indexing specific
		startDate: null,//date to start indexing of projects
		endDate: null,//date to stop indexing of projects
		loopDate: null,//date currently being indexed
		totalDays: 0,//how many days to be proccessed
		completedDays: 0,//how many days we have currently proccessed
		minStars: 100,//how many stars required to be considered a popular project
		canceled: false,//used to determine early halt	
		
		//date range selector 
		crawlStart: 2010,//start year - used for building date range
		crawlEnd: 2019,//end year - used for building date range
		
		//view specific 
		searchTerm: '',//display results that caintain this text 
		page: 1,//page currently being displayed
		totalPages: 1,//total number of pages of results
		totalResults: 0,//total number of results
		showNext: false,//controls the paging links
		showPrev: false,//controls the paging links
		
		gridView: true,//show grid view or list view
		
		 listViewTable: null,//datatable object
            };
        },

        beforeDestroy()
	{
            jQuery(window).unbind('resize', this.NormalizeHeights);//cleanup bindings
        },
 
        methods: {
            NewSearch()
           {
	       this.page=1;//set back to page 1
	       this.ViewProjects();//fetch fresh results
           },
	   IndexGitHub()
           {
	   //Initiate New index of GitHub api
		
		//set date range
		this.startDate = new Date(this.crawlStart, 0, '01');
		this.endDate = new Date(this.crawlEnd,11, '31');
		
		//set start point
		this.loopDate = new Date(this.startDate);
		//determine total days
		this.totalDays=Math.floor((this.endDate-this.startDate) / 86400000);
		//set progress to 0
		this.completedDays=0;
		
		//indexing has not been canceled yet
		this.canceled=false;
		
		//estimate total indexing time considering rate limiting
		this.SetRemainingTime(this.totalDays);
		
		//proccess current date
		this.PopDate();
		//show progress bar
		this.showProgress=true;
		//disable date range slider while scanning
		jQuery( "#dateRange" ).slider( "disable" );
           },
	   DoContinue()
	   {
	   //increment the date by one day 
		this.loopDate.setDate(this.loopDate.getDate() + 1);
		if(!this.canceled && this.loopDate <= this.endDate){//if date is in date range 
		    this.PopDate();//proccess api for this date
		    this.completedDays++;
		}else{//if date is NOT in date range
		    this.showProgress=false;//hide progress bar
		    this.progressMessage='Complete';		    
		    jQuery( "#dateRange" ).slider( "enable" );//reenbable date range selecter
		}
		//set percentage of days proccessed already
		this.percentComplete=((this.completedDays/this.totalDays)*100).toFixed(1);
		
		//estimate remaining indexing time considering rate limiting
		this.SetRemainingTime(this.totalDays-this.completedDays);
		
	   },
	   CancelPopulate()
	   {
	   //cancel database population	   
		this.canceled=true;
		this.showProgress=false;//hide progress bar	
		jQuery( "#dateRange" ).slider( "enable" );//reenbable date range selecter
	   },
	   GridView(doGrid){
		//swap between grid and list views
		this.gridView=doGrid;
		//refresh data on the view you are switching to
		this.RefreshViewData();
	   },
	   RefreshViewData(){
		//refresh data on the view you are on
		if(this.gridView){
		        this.ViewProjects();
		}else{
		    if( this.listViewTable != null){
		        this.listViewTable.ajax.reload();
		    }
		}
	   },
	   LeadingZero(numVal)
	   {
	   //add zeros in front of single digit numbers//return string
		return ((numVal<10)? '0'+numVal: ''+numVal);		
	   },
	   SetRemainingTime(indexDays)
	   {
		let requestsPerPeriod=21;//30 really but we always make sure we can handle up to 1000 results or 10 requests
		if(indexDays > requestsPerPeriod){
		let remainingRateLimitPeriodsNeeded=indexDays/requestsPerPeriod;//calculate number of rate limit periods needed based on limit of requests for rate limit period 
		let remainingHours = Math.floor(remainingRateLimitPeriodsNeeded/60);//calculate hours based on rate limit periods 
		let remainingMinutes = Math.floor(remainingRateLimitPeriodsNeeded%60);//calculate remaining minutes using modulus - remainder after division
		this.remainingTime = remainingHours + " hours " + remainingMinutes + " minutes";//set label for interface
		}else{
		// less than a minute
		this.remainingTime = " less than 1 minute";
		}	
	   },
	   toDayName(dow)
	   {
	   //convert numeric representation of day to human readable format
		let dows = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
		if(dow<7){
		return dows[dow];		
		}else{
		return false;
		}
	   },
	   toMonthName(month)
	   {
	   //convert numeric representation of month to human readable format
		let months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
		if(month<12){
		return months[month];		
		}else{
		return false;
		}
	   },
	   PopDate()
	   {	
		//proccess current date
	    
		let searchDay = this.LeadingZero(this.loopDate.getDate());//add leadign zeros to day
		let searchMonth = this.LeadingZero(1+this.loopDate.getMonth());//increment by one and add leadign zeros to month
		let searchYear = this.loopDate.getFullYear();//grab full year from date
		this.progressMessage='Proccessing - ' + this.toDayName(this.loopDate.getDay()) + ", " + this.toMonthName(this.loopDate.getMonth()) + " " +  searchDay + ", " + searchYear; //set status message to the date we are proccessing
		
		//request our API to fetch all PHP projects with at least as many stars as set in minStars that were created on the specified date
		 axios.post( '/scan' ,{minStars:this.minStars,searchDay:searchDay,searchMonth:searchMonth,searchYear:searchYear}
		).then( response => {	
		    if(response.data.status=='OK'){
			    if(response.data.remainingRateLimit>=10){//verify we can pull 1000 results within rate limit -  at most the api will return 10 pages of 100 results
				this.DoContinue();//proccess next if more remaining
			    }else{
				this.progressMessage='Waitign for next rate limit period -  ' + response.data.remainingRateLimitSeconds + ' seconds';//set status message to notify user we are waitign for rate limit
				setTimeout(function(app){ //wait until the next rate limit period starts before we continue
				app.DoContinue();//proccess next if more remaining
				}, 1 + response.data.remainingRateLimitSeconds * 1000,this);		    
			    }
		    }else{	
		    this.errorMessage='Sorry, an error has occurred.';		    
		    console.log(response.data);
		    this.CancelPopulate();//stop scan
		    }
		    
		   
		

                }).catch((error)=>{    
		    this.errorMessage='Sorry, an error has occurred.';	
		    console.log(error.response);
		    this.CancelPopulate();//stop scan
		});
	   },
	   ViewProjects()
	   {	   
            //fetch results for current page and search term	    
		axios.post( '/view' ,{page:this.page, searchTerm:this.searchTerm}
		).then( response => {
		    this.page=response.data.current_page;//set current page
		    this.totalPages=response.data.last_page;//set total number of pages
		    this.totalResults=response.data.total;//set total results found
		    if(this.page<this.totalPages){this.showNext=true; }else{this.showNext=false;}//determine if we should show next page button
		    if(this.page>1){this.showPrev=true; }else{this.showPrev=false;}//determine if we should show previous page button
		    this.items=response.data.data;//populate our view list
		    setTimeout(function(app){ app.NormalizeHeights();}, 300,this);//after data is populated size the item containers	
		    
                }).catch((error)=>{    
		    this.errorMessage='Sorry, an error has occurred.';	
		    console.log(error.response)
		});	
	    
	   },
	   NextPage()
	   {	   
            //if we are not on last page go to next page
	       if(this.page<this.totalPages){
	           this.page++;//increment page number
	           this.ViewProjects();//display results for page		   
	       } 
	   
	   },
	   PreviousPage()
	   {	   
	   //if we are not on first page go back one page
		if(this.page>1){
		    this.page--;//deincrement page number
		    this.ViewProjects();//display results for page	   
		} 
	   
	   },
	   SetActiveTab(elClass)
	   {
	   //visually select active tab	   
		jQuery('.nav li').removeClass('active');
		jQuery('.'+elClass).addClass('active');
		jQuery('.'+elClass + ' a').tab('show');		
	   },
	   NormalizeHeights()
	   {	   
	   // size all item descriptions to the same size
	   
		var itemHeights=[];
		jQuery('.ItemDescription').each(function() { 
		    jQuery(this).css('min-height', '0'); //remove previous height
		    itemHeights.push(jQuery(this).outerHeight());
		});
		var tallestItem = Math.max.apply(null, itemHeights); 
		jQuery('.ItemDescription').each(function() {
		    jQuery(this).css('min-height',tallestItem + 'px');
		});
	    
	    
		// size all item containers to the same size
		itemHeights=[];
		jQuery('.ItemBorder').each(function() { 		
		    jQuery(this).css('min-height', '0'); //remove previous height
		    itemHeights.push(jQuery(this).outerHeight());
		});
		tallestItem = Math.max.apply(null, itemHeights); 
		jQuery('.ItemBorder').each(function() {
		    jQuery(this).css('min-height',tallestItem + 'px');
		});
	   
	   },NewToken()
	   {
		//refresh csfr token
		axios.post( '/token' ,{}
		).then( response => {
		    if(response.data.status=="OK"){
		        this.SetToken(response.data.token);//assign csfr token to axios and ajax		
		    }
                }).catch((error)=>{   
		    this.errorMessage='Sorry, an error has occurred.';	 
		    console.log(error.response)
		});
	  
            },SetToken(token)
	   {
		//assign csfr token to axios and ajax
		
		if (token) {
		    //set csfr token for axios
		    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
		
		    //set csfr token for ajax 
		    jQuery.ajaxSetup({
		        headers: {
		        'X-CSRF-TOKEN': token
		        }
		    });

		} else {
		    console.log('CSRF token not found');
		}

	  
            }
        }
	
    }
</script>