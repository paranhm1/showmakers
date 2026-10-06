<?php
/** Local-only video regression checks using caller-owned temporary QA fixtures. */
if(!defined('WP_CLI')||!WP_CLI||!showmakers_seo_local())return;
function sm_video_assert($ok,$label){if(!$ok)WP_CLI::error($label);WP_CLI::log('PASS: '.$label);}
$group=acf_get_fields('group_showmakers_projects');$fields=array_column($group,null,'name');
sm_video_assert($fields['project_media_type']['default_value']==='image'&&$fields['project_video']['type']==='file'&&$fields['project_video']['mime_types']==='mp4','Free ACF Image default and MP4-only File field');
sm_video_assert($fields['project_video_poster']['type']==='image'&&!$fields['project_video']['required']&&!$fields['project_video_poster']['required'],'Draft-friendly optional assets; publication validated separately');
foreach(['project_video','project_video_poster'] as $f)sm_video_assert($fields[$f]['conditional_logic'][0][0]['value']==='video','Native Video-only conditional '.$f);
foreach([18,21,26,29,32] as $id)sm_video_assert(showmakers_project_media_type($id)==='image'&&!showmakers_project_video($id),'Existing Project '.$id.' remains Image');
$fixture=isset($args[0])?json_decode(file_get_contents($args[0]),true):null;
if(!$fixture){WP_CLI::success('Existing image records/ACF configuration verified; pass QA fixture JSON for video cases.');return;}
$id=$fixture['project'];sm_video_assert(get_the_title($id)==='LOCAL VIDEO QA ONLY','Only synthetic local Project can be changed by this test');
$base=['project_media_type'=>'video','listing_thumbnail'=>$fixture['listing']??$fixture['poster'],'project_video'=>$fixture['videos']['horizontal'],'project_video_poster'=>$fixture['poster']];
sm_video_assert(!showmakers_video_errors($base,true)&&showmakers_project_video($id),'Approved valid short video and poster');
$long=$base;$long['project_video']=$fixture['videos']['long'];sm_video_assert(showmakers_video_file($long['project_video'])['duration']>10&&showmakers_video_errors($long)['project_video']==='Project videos should be 10 seconds or shorter.','Actual 10.4s MP4 rejected (no rounded-length loophole)');
$wrong=$base;$wrong['project_video']=$fixture['poster'];sm_video_assert(isset(showmakers_video_errors($wrong)['project_video']),'Image/document cannot serve as MP4');
$empty=['project_media_type'=>'video'];sm_video_assert(!showmakers_video_errors($empty,false)&&count(showmakers_video_errors($empty,true))===2,'Incomplete Video Draft allowed, publication requires video/listing thumbnail');
$original=get_post_meta($id);try{
 foreach(['pending','restricted'] as $status){update_post_meta($fixture['videos']['horizontal'],'media_status',$status);sm_video_assert(!showmakers_project_video($id),'Unapproved attachment blocked: '.$status);}
 update_post_meta($fixture['videos']['horizontal'],'media_status','approved');
 foreach(['pending','restricted'] as $status){update_post_meta($id,'media_status',$status);sm_video_assert(!showmakers_project_video($id),'Unapproved Project blocked: '.$status);}
 update_post_meta($id,'media_status','approved');
 $meta=showmakers_seo_metadata(['id'=>$id,'kind'=>'project','url'=>get_permalink($id)]);sm_video_assert($meta['image']['url']===wp_get_attachment_url($fixture['poster'])&&!str_contains(wp_json_encode($meta),'.mp4'),'Video social preview uses approved poster, never MP4');
 update_post_meta($fixture['poster'],'media_status','restricted');$meta=showmakers_seo_metadata(['id'=>$id,'kind'=>'project','url'=>get_permalink($id)]);sm_video_assert(!str_contains(wp_json_encode($meta),wp_get_attachment_url($fixture['poster'])),'Restricted poster/thumbnail cannot appear in SEO');update_post_meta($fixture['poster'],'media_status','approved');
 update_post_meta($id,'project_video',0);wp_update_post(['ID'=>$id,'post_status'=>'publish']);sm_video_assert(get_post_status($id)==='draft','Core/CLI missing-video publication remains Draft');
 update_post_meta($id,'project_video',$fixture['videos']['long']);wp_update_post(['ID'=>$id,'post_status'=>'publish']);sm_video_assert(get_post_status($id)==='draft','Core/CLI oversized-duration publication remains Draft');
 update_post_meta($id,'project_video',$base['project_video']);wp_update_post(['ID'=>$id,'post_status'=>'publish']);sm_video_assert(get_post_status($id)==='publish','Valid Video publication succeeds');
 $_POST=['post_ID'=>$id,'post_type'=>'project','post_status'=>'publish','acf'=>['field_showmakers_p_project_video'=>$fixture['videos']['long']]];acf_reset_validation_errors();do_action('acf/validate_save_post');sm_video_assert((bool)acf_get_validation_errors(),'ACF publish validation exposes clear field error');$_POST=[];acf_reset_validation_errors();
}finally{foreach(['project_media_type','project_video','project_video_poster','media_status'] as $k)update_post_meta($id,$k,$original[$k][0]);foreach([$fixture['videos']['horizontal'],$fixture['poster']]as $a)update_post_meta($a,'media_status','approved');wp_update_post(['ID'=>$id,'post_status'=>'publish']);$_POST=[];}
WP_CLI::success('Video validation/approval/SEO tests complete; synthetic test state restored.');
