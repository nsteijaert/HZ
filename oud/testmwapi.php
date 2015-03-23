<?php
require_once(__DIR__.'/php/Uri.class.php');

$wikiurl='http://127.0.0.1/mediawiki/api.php';

$lgusername='Michael';

$parameters = array(
            'action'=>'login',
            'lgname'=>'Michael',
            'lgpassword'=>'u4095z'
);

$result=Uri::getPostData($wikiurl,$parameters);
var_dump($result);

if ($result['login']['result']!='NeedToken')
{
	die('Communiceren met API niet gelukt!');
}

$login_token=$result['login']['token'];
$cookieprefix=$result['login']['cookieprefix'];
$sessionid=$result['login']['sessionid'];
$lguserid=$result['login']['userid'];

/*setcookie( $cookieprefix . '_session', $sessionid );
setcookie( $cookieprefix . 'UserName', $lgusername );
setcookie( $cookieprefix . 'UserID',   $lguserid );
setcookie( $cookieprefix . 'Token',    $lgtoken );*/

$parameters['lgtoken']=$login_token;
var_dump($parameters);
$result2=Uri::getPostData($wikiurl,$parameters);
var_dump($result2);
?>