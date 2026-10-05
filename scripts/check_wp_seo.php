<?php
/** Read-only LocalWP SEO provider/field/media safety checks. No visibility toggle or content writes. */
if ( ! defined('WP_CLI') || ! WP_CLI || ! showmakers_seo_local() ) return;
function sm_seo_assert($ok,$label){if(!$ok)WP_CLI::error($label);WP_CLI::log('PASS: '.$label);}
sm_seo_assert((int)get_option('blog_public')===0,'Local search visibility discouraged');
sm_seo_assert(!apply_filters('wp_sitemaps_enabled',true),'Local sitemap disabled');
sm_seo_assert((int)get_option('wp_attachment_pages_enabled')===0,'Native attachment pages disabled');
sm_seo_assert(!get_post_type_object('client')->publicly_queryable&&!get_taxonomy('service')->public,'Clients/services internal only');
$group=acf_get_field_group('group_showmakers_seo');
$fields=acf_get_fields($group);
sm_seo_assert(array_column($fields,'type')===array('text','textarea','image'),'Three optional free ACF SEO fields');
foreach($fields as $field)sm_seo_assert(!$field['required'],'Optional '.$field['name']);
$provider=new WP_Sitemaps_Posts();
$pages=$provider->get_url_list(1,'page');$projects=$provider->get_url_list(1,'project');
$expected=array_map(fn($kind)=>home_url($kind==='home'?'/':'/'.$kind.'/'),array_keys(showmakers_seo_pages()));
$actual=array_column($pages,'loc');sort($actual);sort($expected);
sm_seo_assert($actual===$expected&&count($actual)===5,'Core Page sitemap projection: exact five approved URLs');
$slugs=array('short-form-brand-content','tid-group','stories-in-the-moment','ravo-film','ai-assisted-content');
$expected=array_map(fn($slug)=>home_url('/work/'.$slug.'/'),$slugs);$actual=array_column($projects,'loc');sort($actual);sort($expected);
sm_seo_assert($actual===$expected,'Core Project sitemap projection: exact five eligible Projects');
$xml=(new WP_Sitemaps_Renderer())->get_sitemap_xml(array_merge($pages,$projects));
$parsed=simplexml_load_string($xml);
sm_seo_assert($parsed!==false&&count($parsed->url)===10,'Core XML renderer: ten valid URL entries without enabling local HTTP sitemap');
sm_seo_assert(array_keys($provider->get_object_subtypes())===array('page','project'),'No Posts/Clients/attachments sitemap subtypes');
sm_seo_assert(apply_filters('wp_sitemaps_add_provider',new stdClass(),'users')===false&&apply_filters('wp_sitemaps_add_provider',new stdClass(),'taxonomies')===false,'No author/taxonomy sitemap providers');
foreach($slugs as $slug){$p=get_page_by_path($slug,OBJECT,'project');$m=showmakers_seo_metadata(array('kind'=>'project','id'=>$p->ID,'url'=>get_permalink($p)));sm_seo_assert($m['title']===get_the_title($p).' | ShowMakers'&&$m['description']===showmakers_seo_text(get_post_meta($p->ID,'short_summary',true)),'Project fallback '.$slug);}
// Override metadata reads in this process only: never alter Media Library records.
$p=get_page_by_path('ravo-film',OBJECT,'project');$id=(int)get_post_meta($p->ID,'hero_media',true);
$filter=function($value,$object,$key)use($id){return $object===$id&&$key==='media_status'?array('restricted'):$value;};
add_filter('get_post_metadata',$filter,10,3);
sm_seo_assert(!showmakers_seo_image($id),'Restricted image rejected');
$m=showmakers_seo_metadata(array('kind'=>'project','id'=>$p->ID,'url'=>get_permalink($p)));
sm_seo_assert(str_ends_with($m['image']['url'],'/assets/images/showmakers-logo.webp'),'Restricted Project hero falls back to safe brand logo');
remove_filter('get_post_metadata',$filter,10);
$pending=function($value,$object,$key)use($id){return $object===$id&&$key==='media_status'?array('pending'):$value;};
add_filter('get_post_metadata',$pending,10,3);
sm_seo_assert(!showmakers_seo_image($id),'Pending image rejected');
remove_filter('get_post_metadata',$pending,10);
sm_seo_assert(count(showmakers_legacy_routes())===10,'Ten explicit known legacy redirects');
sm_seo_assert((int)get_option('blog_public')===0,'Projection did not enable indexing');
WP_CLI::success('SEO provider projection and safety checks complete; database unchanged.');
