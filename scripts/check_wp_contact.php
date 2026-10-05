<?php
/** Local read-only safety checks. Mail is intercepted; no actual test email is sent. */
if(!defined('WP_CLI') || !WP_CLI || !str_ends_with(wp_parse_url(home_url(),PHP_URL_HOST),'.local'))return;
function sm_contact_assert($ok,$label){if(!$ok)WP_CLI::error($label);WP_CLI::log('PASS: '.$label);}
$rate_keys=[];$sent_keys=[];$mail=[];
$intercept=function($return,$args)use(&$mail){$mail[]=$args;return true;};
add_filter('pre_wp_mail',$intercept,10,2);
function sm_contact_payload(){ $token=(time()-5).'.'.wp_generate_uuid4();return ['contact_nonce'=>wp_create_nonce('showmakers_contact'),'contact_token'=>$token,'contact_signature'=>hash_hmac('sha256',$token,wp_salt('nonce')),'website_honeypot'=>'','name'=>'Local QA','email'=>'qa@example.com','company'=>'Local test','service'=>'website-digital-solutions','goal'=>'Local delivery check.','additionalDetails'=>'No real enquiry.','projectReference'=>'ravo-film','sourcePage'=>'/work/ravo-film/']; }
function sm_contact_run($data,$ip,$origin=null){global $rate_keys,$sent_keys;$rate_keys[]='sm_contact_rate_'.hash_hmac('sha256',$ip,wp_salt('auth'));$sent_keys[]='sm_contact_sent_'.hash_hmac('sha256',$data['contact_token']??'',wp_salt('auth'));return showmakers_contact_process($data,['REMOTE_ADDR'=>$ip,'HTTP_ORIGIN'=>$origin??home_url()]);}
try{
 $base=sm_contact_payload();$r=sm_contact_run($base,'qa-valid');sm_contact_assert($r['success'],'Valid inquiry accepted only after mail acceptance');
 sm_contact_assert($mail[0]['to']===showmakers_site_setting('contact_email'),'Trusted settings recipient');
 sm_contact_assert(in_array('Reply-To: qa@example.com',$mail[0]['headers'])&&!str_contains(implode('\n',$mail[0]['headers']),'From:'),'Validated Reply-To; no user From');
 sm_contact_assert(str_contains($mail[0]['message'],'Project reference: Ravo Film')&&str_contains($mail[0]['message'],'Website & Digital Solutions'),'Verified project/service context');
 sm_contact_assert(sm_contact_run($base,'qa-valid')['status']===409,'Duplicate token rejected');
 foreach(['email'=>"qa@example.com\r\nBcc: x@example.com",'name'=>' ','service'=>'invented','goal'=>str_repeat('x',6001),'projectReference'=>'not-a-project','sourcePage'=>'https://example.com/','contact_nonce'=>'bad','contact_signature'=>'bad','website_honeypot'=>'bot'] as $key=>$value){$p=sm_contact_payload();$p[$key]=$value;$r=sm_contact_run($p,'qa-'.$key);sm_contact_assert(!$r['success'],'Rejected '.$key);if(in_array($key,['email','name','service','goal']))sm_contact_assert(isset($r['errors'][$key]),'Field-specific '.$key.' error');}
 sm_contact_assert(sm_contact_run(sm_contact_payload(),'qa-origin','https://example.com')['status']===403,'Cross-origin browser rejected');
 $p=sm_contact_payload();$p['contact_token']=time().'.'.wp_generate_uuid4();$p['contact_signature']=hash_hmac('sha256',$p['contact_token'],wp_salt('nonce'));sm_contact_assert(sm_contact_run($p,'qa-fast')['status']===429,'Minimum timing');
 $p=sm_contact_payload();$p['contact_token']=(time()-7201).'.'.wp_generate_uuid4();$p['contact_signature']=hash_hmac('sha256',$p['contact_token'],wp_salt('nonce'));sm_contact_assert(sm_contact_run($p,'qa-expired')['status']===403,'Expired form');
 for($i=0;$i<6;$i++){ $p=sm_contact_payload();$p['email']='bad';$r=sm_contact_run($p,'qa-rate'); }sm_contact_assert($r['status']===429,'Five attempts per 15 minutes');
 $p=sm_contact_payload();$p['recipient']='attacker@example.com';$p['name']='<b>Local</b>';sm_contact_run($p,'qa-relay');$last=end($mail);sm_contact_assert($last['to']===showmakers_site_setting('contact_email')&&!str_contains($last['message'],'<b>'),'Recipient input ignored and markup removed');
 remove_filter('pre_wp_mail',$intercept,10);$failure=fn()=>false;add_filter('pre_wp_mail',$failure);$r=sm_contact_run(sm_contact_payload(),'qa-failure');remove_filter('pre_wp_mail',$failure);sm_contact_assert($r['status']===503&&!$r['success'],'Mail failure never returns success');
 $settings=get_option('showmakers_site_settings');$bad=$settings;$bad['contact_email']="bad\r\nBcc: other@example.com";sm_contact_assert(showmakers_sanitize_settings($bad)['contact_email']===$settings['contact_email'],'Invalid global recipient retains previous value');
 sm_contact_assert(!post_type_exists('inquiry'),'No Inquiry CPT');
 WP_CLI::success('Contact validation/security and settings checks complete; mail intercepted only.');
}finally{remove_filter('pre_wp_mail',$intercept,10);foreach(array_unique(array_merge($rate_keys,$sent_keys)) as $k)delete_transient($k);}
