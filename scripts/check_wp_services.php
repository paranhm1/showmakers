<?php
/** Read-only local Phase 6 reconciliation and fault-injection checks. */
if ( !defined('WP_CLI') || !WP_CLI ) exit;
if (wp_parse_url(home_url(),PHP_URL_HOST)!=='showmakers-local.local') WP_CLI::error('Local only.');
function sm_service_assert($condition,$label){if(!$condition)WP_CLI::error($label);WP_CLI::log('PASS: '.$label);}
$root=dirname(__DIR__);$sources=json_decode(file_get_contents($root.'/data/services.json'),true);
$terms=showmakers_visible_services();
sm_service_assert(wp_list_pluck($terms,'slug')===array_column($sources,'slug'),'Eight visible services in source sort order');
$attachments=array();
foreach($sources as $source){
 $term=get_term_by('slug',$source['slug'],'service');$id=$term->term_id;
 sm_service_assert(html_entity_decode($term->name,ENT_QUOTES,'UTF-8')===$source['title'],'Stable term label '.$id);
 foreach(array('service_intro'=>$source['shortDescription'],'service_description'=>$source['description'],'capabilities'=>implode("\n",$source['deliverables']),'platforms'=>implode("\n",$source['platforms']),'formats'=>implode("\n",$source['formats']??array()),'sort_order'=>$source['order'],'visible'=>1) as $field=>$value)sm_service_assert((string)get_term_meta($id,$field,true)===(string)$value,$source['slug'].' '.$field);
 foreach($source['media'] as $i=>$item){$field=$i===0?'service_media':'service_media_2';$aid=(int)get_term_meta($id,$field,true);sm_service_assert(showmakers_approved_media($aid)&&get_post_meta($aid,'_showmakers_source_asset',true)===$item['src'],'Correct approved service media');sm_service_assert(get_term_meta($id,$field.'_caption',true)===$item['caption'],'Usage caption preserved without changing attachment caption');$attachments[]=$aid;}
 sm_service_assert(count(showmakers_service_passages($term))===count($source['subsections']),'All platform passages retained');
 foreach(showmakers_service_passages($term) as $i=>$passage)sm_service_assert($passage['title']===$source['subsections'][$i]['title']&&$passage['text']===$source['subsections'][$i]['text'],'Exact approved platform passage');
 if(!$source['media'])sm_service_assert(!get_term_meta($id,'service_media',true)&&!get_term_meta($id,'service_media_2',true),'Typography-led service without filler');
}
sm_service_assert(count(array_unique($attachments))===5,'Five existing approved attachments reused');
sm_service_assert(showmakers_service_lines(" One \r\n\r\n Two \n  \n")==array('One','Two'),'Textarea trims whitespace and skips blank lines');
sm_service_assert(showmakers_service_lines("<script>bad</script>\nSafe")==array('Safe'),'Uncontrolled tags removed');
$projects=showmakers_visible_projects();
foreach(array('media-production'=>2,'website-digital-solutions'=>2,'ai-enhanced-content-production'=>1) as $slug=>$count)sm_service_assert(showmakers_service_project_count($slug)===$count,'Unchanged real Project count '.$slug);
sm_service_assert(count($projects)===5,'Five real projects remain eligible');
// Read-only visibility simulation, never updates database fields or relationships.
$hidden=get_term_by('slug','media-production','service');
$guard=function($value,$id,$key)use($hidden){return $id===$hidden->term_id&&$key==='visible'?'0':$value;};
add_filter('get_term_metadata',$guard,10,3);
try{
 sm_service_assert(count(showmakers_visible_services())===7,'Hidden service omitted from shared collection');
 $project=get_page_by_path('short-form-brand-content',OBJECT,'project');
 sm_service_assert(has_term('media-production','service',$project->ID)&&showmakers_service_project_count('media-production')===2,'Hidden term assignments/counts remain internally intact');
 sm_service_assert(showmakers_project_services($project->ID)===array(),'Hidden public project service labels/links omitted');
 ob_start();get_template_part('template-parts/services-cms',null,array('services'=>showmakers_visible_services(),'projects'=>$projects));$html=ob_get_clean();
 sm_service_assert(strpos($html,'id="media-production"')===false&&strpos($html,'href="#media-production"')===false,'Hidden service omitted from Explorer markup');
 ob_start();get_template_part('template-parts/home-static');$html=ob_get_clean();
 sm_service_assert(strpos($html,"#media-production")===false,'Hidden service omitted from Home index');
}finally{remove_filter('get_term_metadata',$guard,10);}
// Attachment permission enforced for service placements, without editing actual media.
$guard=function($value,$id,$key){return $id===20&&$key==='media_status'?'restricted':$value;};add_filter('get_post_metadata',$guard,10,3);
try{ob_start();get_template_part('template-parts/services-cms',null,array('services'=>showmakers_visible_services(),'projects'=>$projects));$html=ob_get_clean();sm_service_assert(strpos($html,wp_get_attachment_url(20))===false,'Restricted service media omitted');}finally{remove_filter('get_post_metadata',$guard,10);}
sm_service_assert(count(showmakers_visible_services())===8,'Fault-injection removed; eight services still visible');
WP_CLI::success('Phase 6 service content and visibility checks complete.');
