<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api'
], function($api) {
    // 短信验证码
    // return response('this is version v1');
	$api->group([
		'middleware' => 'api.throttle',
		'limit'	=> config('api.rate_limits.sign.limit'),
		'expire' => config('api.rate_limits.sign.expire'),
	], 
	function($api){
		// 短信验证码
	    $api->post('verificationCodes', 'VerificationCodesController@store')
	        ->name('api.verificationCodes.store');

	    // 用户注册    
	    $api->post('users', 'UsersController@store')
	    	->name('api.user.store');

		// 图片验证码
		$api->post('captchas', 'CaptchasController@store')
	    	->name('api.captchas.store');  
	    	
		// 第三方登录
		$api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
		    ->name('api.socials.authorizations.store');	    	  	
	});
});
