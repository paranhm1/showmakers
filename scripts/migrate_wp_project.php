<?php
/** Explicit one-project local migration. wp eval-file scripts/migrate_wp_project.php <source-id>. Never publishes. */
if (!defined('WP_CLI') || !WP_CLI) exit;
if (wp_parse_url(home_url(), PHP_URL_HOST) !== 'showmakers-local.local' || !function_exists('update_field')) WP_CLI::error('Authorized LocalWP and free ACF required.');
$allow = array('brand-content'=>array('brand-content.webp'), 'tid-group'=>array('tid-detail.webp','tid-home.webp'), 'short-form'=>array('short-form.webp'), 'ai-content'=>array('ai-content.webp'));
$key = $args[0] ?? '';
if (!isset($allow[$key])) WP_CLI::error('Choose exactly one approved remaining project.');
$root=dirname(__DIR__); $source=null;
foreach(json_decode(file_get_contents($root.'/data/projects.json'),true) as $record) if($record['id']===$key) $source=$record;
if(!$source || !$source['published'] || $source['isPlaceholder'] || !empty($source['developmentOnly']) || $source['mediaStatus']!=='approved') WP_CLI::error('Source is not approved real public work.');
if(get_page_by_path($source['slug'],OBJECT,'project') || get_posts(array('post_type'=>'project','post_status'=>'any','meta_key'=>'source_id','meta_value'=>$key))) WP_CLI::error('Record exists; refusing to overwrite staff edits.');
$terms=array(); foreach($source['services'] as $slug) { $term=get_term_by('slug',$slug,'service'); if(!$term) WP_CLI::error('Missing existing service.'); $terms[]=(int)$term->term_id; }
$media=array(); foreach(array_merge(array($source['listingThumbnail'],$source['heroMedia']),$source['media']) as $item) { if(!in_array(basename($item['src']),$allow[$key],true) || $item['src']!=='assets/images/work/'.basename($item['src']) || $item['type']!=='image' || !is_file($root.'/'.$item['src'])) WP_CLI::error('Unexpected media.'); $media[$item['src']]=$item; }
$extras=array_values(array_filter($source['media'],function($item)use($source){return $item['src']!==$source['heroMedia']['src'];}));
if(count($extras)>5) WP_CLI::error('Supporting media exceeds five slots; review subset first.');
$keys=array(); foreach(array('projects','clients','media') as $group) { $json=json_decode(file_get_contents($root.'/wordpress/wp-content/plugins/showmakers-content/acf-json/group_showmakers_'.$group.'.json'),true); foreach($json['fields'] as $field) if(!empty($field['name'])) $keys[$group][$field['name']]=$field['key']; }
$client=0;
if($source['client']) {
 if($key!=='tid-group' || $source['client']!=='TID Group') WP_CLI::error('Unreviewed Client identity.');
 $existing=get_posts(array('post_type'=>'client','post_status'=>'any','meta_key'=>'source_id','meta_value'=>'tid-group','numberposts'=>-1));
 $same=get_page_by_path('tid-group',OBJECT,'client');
 if(count($existing)>1 || ($same && (!$existing || $same->ID!==$existing[0]->ID))) WP_CLI::error('Ambiguous existing Client; inspect before joining.');
 if($existing) { if($existing[0]->post_title!=='TID Group') WP_CLI::error('Client identity mismatch.'); $client=$existing[0]->ID; }
 else { foreach(get_posts(array('post_type'=>'client','post_status'=>'any','numberposts'=>-1)) as $c) if($c->post_title==='TID Group') WP_CLI::error('Unmapped matching Client: inspect first.'); $client=wp_insert_post(array('post_type'=>'client','post_status'=>'publish','post_title'=>'TID Group','post_name'=>'tid-group'),true); if(is_wp_error($client)) WP_CLI::error($client->get_error_message()); update_post_meta($client,'source_id','tid-group'); foreach(array('visible'=>1,'sort_order'=>1,'logo_status'=>'pending') as $name=>$value) update_field($keys['clients'][$name],$value,$client); }
}
require_once ABSPATH.'wp-admin/includes/file.php'; require_once ABSPATH.'wp-admin/includes/media.php'; require_once ABSPATH.'wp-admin/includes/image.php';
$ids=array(); foreach($media as $relative=>$item) {
 $existing=get_posts(array('post_type'=>'attachment','post_status'=>'inherit','meta_key'=>'_showmakers_source_asset','meta_value'=>$relative,'fields'=>'ids','numberposts'=>-1));
 if(count($existing)>1) WP_CLI::error('Duplicate source attachment.');
 if($existing) $id=$existing[0]; else { $temporary=wp_tempnam(basename($relative)); if(!copy($root.'/'.$relative,$temporary)) WP_CLI::error('Copy failed.'); $id=media_handle_sideload(array('name'=>basename($relative),'tmp_name'=>$temporary),0); if(is_wp_error($id)) WP_CLI::error($id->get_error_message()); update_post_meta($id,'_showmakers_source_asset',$relative); update_post_meta($id,'_wp_attachment_image_alt',$item['alt']); wp_update_post(array('ID'=>$id,'post_excerpt'=>$item['caption'])); update_field($keys['media']['media_status'],'approved',$id); }
 if(!showmakers_approved_media($id) || hash_file('sha256',get_attached_file($id))!==hash_file('sha256',$root.'/'.$relative)) WP_CLI::error('Approval/integrity mismatch.'); $ids[$relative]=$id;
}
$project=wp_insert_post(array('post_type'=>'project','post_status'=>'draft','post_title'=>$source['title'],'post_name'=>$source['slug']),true); if(is_wp_error($project)) WP_CLI::error($project->get_error_message());
$fields=array('client'=>$client,'short_summary'=>$source['summary'],'sort_order'=>$source['order'],'services'=>$terms,'listing_thumbnail'=>$ids[$source['listingThumbnail']['src']],'listing_fit'=>$source['listingThumbnail']['fit'],'hero_media'=>$ids[$source['heroMedia']['src']],'featured'=>(int)$source['featured'],'media_status'=>'approved');
foreach($extras as $i=>$item) $fields['project_image_'.($i+1)]=$ids[$item['src']];
foreach($fields as $name=>$value) update_field($keys['projects'][$name],$value,$project);
foreach(array('source_id'=>$key,'presentation'=>$source['presentation'],'documentation_note'=>$source['documentationNote'],'_showmakers_provenance'=>$source['source']) as $name=>$value) update_post_meta($project,$name,$value);
set_post_thumbnail($project,$fields['listing_thumbnail']); foreach($ids as $id) wp_update_post(array('ID'=>$id,'post_parent'=>$project));
WP_CLI::log(wp_json_encode(array('project'=>$project,'client'=>$client,'attachments'=>$ids,'status'=>'draft','preview'=>get_preview_post_link($project)),JSON_PRETTY_PRINT));
