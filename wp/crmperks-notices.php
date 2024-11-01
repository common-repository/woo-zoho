<?php
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'vx_crmperks_notice_vxc_zoho' )):
class vx_crmperks_notice_vxc_zoho extends vxc_zoho{
public $plugin_url="https://www.crmperks.com";
public $review_link='https://wordpress.org/support/plugin/woo-zoho/reviews/?filter=5#new-post';

public function __construct(){
  add_action('add_section_tab_wc_'.$this->id, array($this, 'add_section_wc'),99);
  add_action('crmperks_wc_settings_end_'.$this->id, array($this, 'notice'),99);
  add_action( 'wp_ajax_review_dismiss_'.$this->id, array( $this, 'review_dismiss' ) );
  add_action( 'vx_plugin_upgrade_notice_plugin_'.$this->type, array( $this, 'upgrade_notice' ) );
     // html section
  add_filter( 'add_section_html_'.$this->id, array( $this, 'license_section_wc' ) );
  add_filter( 'menu_links_'.$this->id, array( $this, 'menu_link' ) );
  add_filter( 'plugin_row_meta', array( $this , 'pro_link' ), 10, 2 );
}
   public function add_section_wc($tabs){
       $this->review_notice();  
    $tabs["vxc_notice"]='<b>'.__('Go Premium','woocommerce-base-crm').'</b>';
    return $tabs;
}
public function license_section_wc($page_added){
    if(!$page_added){
        global $current_section;
        if($current_section == 'vxc_notice'){
            $this->notice();
          $page_added=true;
        } 
    }
return $page_added;
}
public function upgrade_notice(){
 $plugin_url=$this->plugin_url.'?vx_product='.$this->domain;
?>
<style type="text/css">
.vx_pro_version .fa{
color: #727f30; font-size: 18px; vertical-align: middle;   
} 
</style>
 <div class="updated below-h2 vx_pro_version" style="border: 1px solid  #1192C1; border-left-width: 6px; padding: 2px 12px; margin-top: 20px;">
<h2 style="font-size: 22px;">Premium Version</h2>
<p><i class="fa fa-check"></i> Add WooCommerce Order Items to Zoho.</p>
<p><i class="fa fa-check"></i> All Zoho modules like Invoices, Customer Payments, Estimates, Credit Notes, Recurring Invoices, custom modules etc.</p>
<p><i class="fa fa-check"></i> Zoho custom fields.</p>
<p><i class="fa fa-check"></i> Zoho Phone fields.</p>
<p><i class="fa fa-check"></i> Select Zoho Object Layout.</p>
<p><i class="fa fa-check"></i> Add a lead to campaign in Zoho CRM.</p>
<p><i class="fa fa-check"></i> Assign owner to any object(Contact, lead , account etc) in Zoho CRM.</p>
<p><i class="fa fa-check"></i> Assign object created/updated/found by one feed to other feed. For example assigning a contact to a custom Zoho object.</p>
<p>By purchasing the premium version of the plugin you will get access to advanced marketing features and you will get one year of free updates & support</p>
<p>
<a href="<?php echo esc_attr($plugin_url) ?>" target="_blank" class="button-primary button">Go Premium</a>
</p>
</div>
<?php   
}
public function notice(){
    $this->upgrade_notice();
?>
<div class="updated below-h2" style="border: 1px solid  #1192C1; border-left-width: 6px; padding: 20px 12px;">
<h3>Our Other Free Plugins</h3>
<p><b><a href="https://wordpress.org/plugins/crm-perks-forms/" target="_blank">CRM Perks Forms</a></b> is lightweight and highly optimized contact form builder with Poups and floating buttons.</p>
<p><b><a href="https://wordpress.org/plugins/gf-zoho/" target="_blank">Gravity Forms Zoho</a></b> Integrates Zoho crm with Gravity forms.</p>
<p><b><a href="https://wordpress.org/plugins/cf7-zoho/" target="_blank">Contact Form Zoho</a></b> Integrates Zoho crm with contact form 7.</p>


</div>
<?php
}

public function review_dismiss(){
$install_time=get_option($this->id."_install_data");
if(!is_array($install_time)){ $install_time =array(); }
$install_time['review_closed']='true';
update_option($this->id."_install_data",$install_time,false);
die();
}

public function review_notice() { 
 $install_time=get_option($this->id."_install_data");
   if(!is_array($install_time)){ $install_time =array(); }
   if(empty($install_time['time'])){
       $install_time['time']=current_time( 'timestamp' , 1 );
      update_option($this->id."_install_data",$install_time,false); 
   }
   
    $time=current_time( 'timestamp' , 1 )-(7200*16);
//$install_time['review_closed']='';
 if(!empty($install_time) && is_array($install_time) && !empty($install_time['time']) && empty($install_time['review_closed'])){ 
   $time_i=(int)$install_time['time'];
    if($time > $time_i){ 
        ?>
        <div class="notice notice-info is-dismissible vxcf-review-notice">
  <p><?php echo sprintf(__( 'You\'ve been using WooCommerce zoho Plugin for some time now; we hope you love it!.%s If you do, please %s leave us a %s rating on WordPress.org%s to help us spread the word and boost our motivation.','contact-form-entries'),'<br/>','<a href="'.$this->review_link.'" target="_blank" rel="noopener noreferrer">','&#9733;&#9733;&#9733;&#9733;&#9733;','</a>'); ?></p>
  <p><a href="<?php echo $this->review_link ?>"  target="_blank" class="vxcf_close_notice_a" rel="noopener noreferrer"><?php esc_html_e('Yes, you deserve it','contact-form-entries') ?></a> | <a href="#" class="vxcf_close_notice_a"><?php esc_html_e('Dismiss this notice','contact-form-entries'); ?></a></p>
        </div>
        <script type="text/javascript">
            jQuery( document ).ready( function ( $ ) {
                $( document ).on( 'click', '.vxcf-review-notice .vxcf_close_notice_a', function ( e ) {
                       //e.preventDefault(); 
                       $('.vxcf-review-notice .notice-dismiss').click();
 //$.ajax({ type: "POST", url: ajaxurl, async : false, data: {action:"vxcf_form_review_dismiss"} });          
        $.post( ajaxurl, { action: 'review_dismiss_<?php echo esc_attr($this->id) ?>' } );
                } );
            } );
        </script>
        <?php
    } }
} 
public function pro_link($links,$file){
    $slug=$this->get_slug();
    if($file == $slug){
        $url=$this->plugin_url.'?vx_product='.$this->domain;
      //  $url=admin_url('admin.php?page=vx-addons');
        $links[]='<a href="'.$url.'"><b>Go Premium</b></a>';
    }
   return $links; 
}
public function menu_link($links){
     $url=$this->plugin_url.'?vx_product='.$this->domain;
   $links[]=array("title"=>'<b>Go Premium</b>',"link"=>$url );
    return $links;
}

}
new vx_crmperks_notice_vxc_zoho();
endif;
