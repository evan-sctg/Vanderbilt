Create a README file with a description of your architecture and notes on
installation of your application. You are free to use any PHP, JavaScript, or
CSS frameworks as you see fit.



I built a web application that's powered by Laravel and Vue.js 
The main interface Allows you to scan for new projects using a date range selector and minimum star count.
You can also Browse and search through previously scanned projects, both grid and list views are available.
In grid view, you have the option to go to the repository page and in list view, you have the option to go into a detailed view.
				
To protect against SQL injection it uses PDO prepared statements. 
When creating interfaces I use either blade templates which escape template variables 
by default or I use Vue.js components which cast its templates as strings and are never executed by the browser.
All of the API endpoints and interfaces have CSFR token handling and validation which our ajax requests validate.


Built using CentOS7, PHP7, MySQL/MarinaDB, Apache 2.4 , Laravel, Vue.js

Laravel requirements:
- PHP >= 7.1.3
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- BCMath PHP Extension





1) install composer
2) install node using node version manager
3) Upload project folder "application" to server
4) Edit "application/.env" file  (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_URL)
5) Point apache to the "application/public" directory and set AllowOverride to allow htaccess rewites
6) Navigate to the "application" directory and run the command "composer update" to regenerate vendor files
7) In the "application" directory and run the command "npm install" to install node dependencies
8) In the "application" directory and run the command "php artisan migrate" to build the database structure
9) In the "application" directory and run the command "npm run dev" to build the application
10) Navigate in your browser to the path that you previously pointed to "application/public" where you can now scan or browse projects


Primary files:
- /routes/web.php
- /app/Http/Controllers/ProjectsController.php
- /app/Classes/GitHubSearchResponse.php
- /resources/js/components/GitHubAPIInterface.vue
- /resources/views/details.blade.php
- /database/migrations/2019_2_2_000000_create_projects_table.php