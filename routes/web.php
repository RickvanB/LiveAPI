<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->group(['prefix' => 'v1', ['middleware' => 'auth']], function () use ($app) {

	/* GET REQUESTS */
	$app->get('/', function () use ($app) {
    	return 'Hello world!';
	});

	$app->get('/program/{day}/{daytime}/{poule}/{limit}', 'ApiController@getProgram');

	$app->get('/results/{day}/{daytime}/{poule}/{limit}', 'ApiController@getResults');

	$app->get('/poule/results/{day}/{poule}/{limit}', 'ApiController@getPouleResults');

	$app->get('/export/{day}/{daytime}/{poule}', 'ApiController@getDataforExport');

	$app->get('/export/results/{day}/{daytime}/{poule}', 'ApiController@getDataforExportResults');

	$app->get('/cron/lastupdated', 'ApiController@getLastUpdated');
	$app->get('/cron/updates', 'ApiController@cronUpdates');

	$app->get('/accounts/facebook', 'ApiController@countFacebookAccounts');
	$app->get('/accounts/count/all', 'ApiController@countDefaultAccounts');

	$app->get('/teams/all', 'ApiController@getTeams');
	$app->get('/teams/search/insert', 'ApiController@searchQueryTeam');

	$app->get('/results/latest', 'ApiController@getLatestResults');

	// Notifications
	$app->post('/notifications/send', 'NotificationsController@sendNotificationMessage');

	/* PUT REQUESTS */


	/* POST REQUESTS */
	$app->post('/uitslagen/invoeren', 'ApiController@insertResults');
});
