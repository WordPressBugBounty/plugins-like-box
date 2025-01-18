<?php 

    /*############ Social Like Box Admin Menu Class ################*/

class like_box_admin_menu{
	
	private $menu_name;
	private $databese_parametrs;
	private $plugin_url;
	private $text_parametrs;

	/*############ Construct Function ##################*/
	
	function __construct($param){
		
		$this->text_parametrs=array(
			'parametrs_sucsses_saved'=>'Successfully saved.',
			'error_in_saving'=>'can\'t save "%s" plugin parameter<br>',
			'missing_title'=>'Type Message Title',
			'missing_fromname'=>'Type From Name',
			'missing_frommail'=>'Type From mail',
			'mising_massage'=>'Type Message',
			'sucsses_mailed'=>'Your message was sent successfully.',
			'error_maied'=>'Error sending email',
			'authorize_problem' => 'Authorization Problem'
		);		
		
		$this->menu_name=$param['menu_name'];
		$this->databese_parametrs=$param['databese_parametrs'];
		if(isset($params['plugin_url']))
			$this->plugin_url=$params['plugin_url'];
		else
			$this->plugin_url=trailingslashit(dirname(plugins_url('',__FILE__)));


		// Insert button
		add_action('media_buttons', array($this,'like_box_button'));
		add_action( 'wp_ajax_like_box_window_manager', array($this,'like_box_window_insert_content') );
		
		add_action( 'wp_ajax_like_box_page_save', array($this,'save_in_databese') );
		add_action( 'wp_ajax_like_box_send_mail', array($this,'sending_mail') );
	}
	
	/*############################### Insert button function ########################################*/
	
	public function like_box_button($context) {
	  
	  $img = $this->plugin_url. 'images/post.button.png';
	
	  $title = 'Add Like Box';
	
	  $context = '<a class="button thickbox" title="Create facebook like box and insert in posts/pages"    href="'.admin_url("admin-ajax.php").'?action=like_box_window_manager&height=750&width=640">
			<span class="wp-media-buttons-icon" style="background: url('.$img.'); background-repeat: no-repeat; background-position: left bottom;"></span>
		Add like box
		</a>';  
	  echo $context;
	}

	/*############################### Insert social like box - content function ########################################*/	
	
	public function like_box_window_insert_content(){
		?>
        <style>
        #miain_like_box_window_manager > tbody > tr:nth-child(odd) {
		  background-color: rgba(176, 176, 176, 0.07);
		}
		#miain_like_box_window_manager>tfoot>tr>td{
			border-top:1px solid #ccc;
		}
		#TB_window{  
			overflow-y: auto;
		}
		#TB_ajaxContent{
			width:95% !important;
		}
		.wp-picker-holder{
			position: absolute;
 			z-index: 100000;
		}
		.desription_class{
			float: right;
			cursor: default;
			color: #0074a2;
			font-size: 18px;
			font-weight: bold;
			border: 1px solid #000000;
			border-radius: 200px;
			height: 20px;
			padding-left: 6px;
			padding-right: 6px;
			margin-left: 15px;
		}
		.pro_feature {
		  font-size: 13px;
		  font-weight: bold;
		  color: #7052fb;
		}
		.input_placholder_small{ width: 85%;}
		.input_placholder_small::-webkit-input-placeholder { font-size:11px; }
		.input_placholder_small::-moz-placeholder {font-size:11px; } /* firefox 19+ */
		.input_placholder_small:-ms-input-placeholder { font-size:11px; } /* ie */
		.input_placholder_smallinput:-moz-placeholder { font-size:11px; }
        </style>
        <script>var pro_text='"If you want to use this feature upgrade to Like box Pro"'</script>
			<table id="miain_like_box_window_manager" class="wp-list-table widefat fixed posts section_parametrs_table">  
                <tbody> 
                    <tr>
                        <td>
                            Page ID <span class="desription_class" title="Type here Facebook fan page url(without https://www.facebook.com/ or https://web.facebook.com, if your Facebook fan page url is https://web.facebook.com/BMW then type here just BMW).">?</span>
                        </td>
                        <td>
                           <input id="like_box_profile_id" type="text" value="" class="widefat">
                        </td>                
                    </tr>
                    <tr>
                        <td>
                         	Like box Animation <span class="pro_feature"> (pro)</span><span  class="desription_class" title="Select the animation type of the Facebook like box.">?</span>
                        </td>
                        <td>
                          <?php  like_box_setting::generete_animation_select('animation_efect','none') ?>
                        </td>                
                    </tr>
                    <tr>
                        <td>
                         	Like box border <span class="pro_feature"> (pro)</span><span title="Show Facebook like box border." class="desription_class">?</span>
                        </td>
                        <td>
                            <select onMouseDown="alert(pro_text); return false;" id="like_box_show_border">
                                <option selected="selected" value="show">Show</option>
                                <option value="hide">Hide</option>
                            </select>
                        </td>                
                    </tr>
                     <tr>
                        <td>
                         	Like box border color <span class="pro_feature"> (pro)</span><span title="Select the Border Color of Facebook Like box." class="desription_class">?</span>
                        </td>
                        <td>
                          <div onClick="alert(pro_text); return false;" class="disabled_picker">
                               <div class="wp-picker-container disabled_picker">
									<button type="button" class="button wp-color-result" aria-expanded="false" style="background-color: rgb(255, 255, 255);"><span class="wp-color-result-text">Select Color</span></button>
								</div>
                            </div>
                        </td>                
                    </tr>
                    <tr>
                        <td>
                         	Facebook latest posts <span  class="desription_class" title="Show the Latest Posts from Facebook.">?</span>
                        </td>
                        <td>
                          	<select id="like_box_stream">
                                <option  value="show">Show</option>
                                <option selected="selected" value="hide">Hide</option>
                            </select>
                        </td>                
                    </tr>
                     <tr>
                        <td>
                         	Show the Users Faces <span title="Choose to show or hide Users Faces" class="desription_class">?</span>
                        </td>
                        <td>
                        	<select  id="like_box_connections">
                                <option selected="selected" value="show">Show</option>
                                <option value="hide">Hide</option>
                            </select>
                        </td>                
             		</tr>
                     <tr id="like_box_static_width">
                        <td>
                         	Like box width <span title="Type here Facebook Like box width(px)" class="desription_class">?</span>
                        </td>
                        <td>
                          <input placeholder="The pixel width of the embed (Min. 180px to Max. 500px)" id="like_box_width"  type="text" value="300" class="input_placholder_small" ><small>(px)</small>
                        </td>                
                    </tr>
                    <tr>
                        <td>
                         	Like box height <span title="Type here Facebook Like box height(px)" class="desription_class">?</span>
                        </td>
                        <td>
                          <input placeholder="The pixel height of the embed (Min. 70px)" id="like_box_height" type="text" value="550" class="input_placholder_small" ><small>(px)</small>
                        </td>                
                    </tr>
                     <tr>
                        <td>
                         	Like box header <span title="Select the Like box header size(Small/Big)" class="desription_class">?</span>
                        </td>
                        <td>
                          <select id="like_box_header_size">
                                <option selected="selected" value="small">Small</option>
                                <option value="big">Big</option>
                          </select>
                        </td>                
                    </tr>
                     <tr>
                        <td>
                         	Like box cover photo <span title="Choose to show/hide Like box cover photo" class="desription_class">?</span>
                        </td>
                        <td>
                          <select id="like_box_cover_photo">
                                <option selected="selected" value="show">Show</option>
                                <option value="hide">Hide</option>
                          </select>
                        </td>                
                    </tr>
                    <tr>
                        <td>
                         	Language <span title="Type here the Facebook Like box language code. If you left this field blank, the default language will be displayed." class="desription_class">?</span>
                        </td>
                        <td>
                         <input id="like_box_locale" type="text" value="en_US" class="" size="4"><small>(en_US, de_DE...)</small>
                        </td>                
                    </tr>
                </tbody>
               <tfoot>
                	<tr>                      
                        <td colspan="2">
                        	 <div style="display:inline-block; float:left;" class="mceActionPanel"><input type="button" id="cancel" name="cancel" value="Insert Like Box" class="button button-primary" onClick="insert_like_box();"/></div>
                             <span style="float:right"><a href="https://wpdevart.com/wordpress-facebook-like-box-plugin/" target="_blank" style="color: #7052fb; font-weight: bold; font-size: 18px; text-decoration: none;">Upgrade to Pro Version</a><br></span>
                        </td>                
                    </tr>
                </tfoot>
            </table>         
    
                   
    
    <script type="text/javascript">
	
	
       jQuery('#TB_window').css('max-height',(jQuery('#miain_like_box_window_manager').height()+66)+'px');
	   jQuery('#TB_ajaxContent').css('max-height',(jQuery('#miain_like_box_window_manager').height()+16)+'px');
	   jQuery('#miain_like_box_window_manager').ready(function(e) {
                jQuery(".color_my_likbox").wpColorPicker();
        });
        function insert_like_box() {
          
			if(jQuery('#like_box_profile_id').val()!=''){
                var tagtext;
				var generete_atributes = 'profile_id="'+jQuery('#like_box_profile_id').val()+'" stream="'+jQuery('#like_box_stream').val()+'" connections="'+jQuery('#like_box_connections').val()+'" width="'+jQuery('#like_box_width').val()+'" height="'+jQuery('#like_box_height').val()+'" header="'+jQuery('#like_box_header_size').val()+'" cover_photo="'+jQuery('#like_box_cover_photo').val()+'" locale="'+jQuery('#like_box_locale').val()+'"'

                tagtext = '[wpdevart_like_box '+generete_atributes+']';
                window.send_to_editor(tagtext);
              	tb_remove()
            }
			else{
				alert('Page id field is required')				
			}
        }

    </script>
    </body>
    </html>
<?php
die;	
}

	/*############################### Create menu function ########################################*/	
	
	public function create_menu(){
		global $submenu;
		$sub_men_cap=str_replace( ' ', '-', $this->menu_name);
		$main_page 	 	  = add_menu_page( $this->menu_name, $this->menu_name, 'manage_options', str_replace( ' ', '-', $this->menu_name), array($this, 'main_menu_function'),$this->plugin_url.'images/facebook_menu_icon.png');
		add_submenu_page($this->menu_name,  $this->menu_name,  $this->menu_name, 'manage_options', str_replace( ' ', '-', $this->menu_name), array($this, 'main_menu_function'));
		$featured_page	  = add_submenu_page( str_replace( ' ', '-', $this->menu_name), 'Featured Plugins', 'Featured Plugins', 'manage_options', 'like-box-featured-plugins', array($this, 'featured_plugins'));
		$featured_theme_page	  = add_submenu_page( str_replace( ' ', '-', $this->menu_name), 'Featured Themes', 'Featured Themes', 'manage_options', 'like-box-featured-themes', array($this, 'featured_themes'));
		$hire_expert	  = add_submenu_page( str_replace( ' ', '-', $this->menu_name), 'Hire an Expert', '<span style="color:#00ff66" >Hire an Expert</span>', 'manage_options', 'like-box-hire-expert', array($this, 'hire_expert'));
		add_action('admin_print_styles-' .$main_page, array($this,'menu_requeried_scripts'));
		add_action('admin_print_styles-' . $featured_page, array($this, 'featured_plugins_js_css'));
        add_action('admin_print_styles-' . $featured_theme_page, array($this, 'featured_themes_js_css'));
        add_action('admin_print_styles-' . $hire_expert, array($this, 'hire_expert_js_css'));
		
		if(isset($submenu[$sub_men_cap]))
			add_submenu_page( $sub_men_cap, "Support or Any Ideas?", "<span style='color:#00ff66' >Support or Any Ideas?</span>", 'manage_options',"wpdevart_like_box_any_ideas",array($this, 'any_ideas'),155);
		if (isset($submenu[$sub_men_cap])) {
			$count_pages = count($submenu[$sub_men_cap])-1;
			$submenu[$sub_men_cap][$count_pages][2] = wpdevart_likebox_support_url;
		}
	}
	public function any_ideas(){
		
	}

	public function featured_plugins_js_css() {
        wp_enqueue_style('wpda_like_box_featured_page_css', $this->plugin_url . 'includes/style/featured_plugins_css.css');
    }

    public function featured_themes_js_css() {
        wp_enqueue_style('wpda_like_box_themes_page_css', $this->plugin_url . 'includes/style/featured_themes_css.css');
    }

    public function hire_expert_js_css() {
        wp_enqueue_style('wpda_like_box_expert_css', $this->plugin_url . 'includes/style/hire_expert.css');
    }
	/*###################### Required scripts function ##################*/	
	
	public function menu_requeried_scripts(){
		wp_enqueue_script('wp-color-picker');		
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'like-box-admin-script' ); 
		wp_enqueue_style('like-box-admin-style');
	}

	/*############################### Generate parameters function ########################################*/	
	
	private function generete_parametrs($page_name){
		$page_parametrs=array();
		if(isset($this->databese_parametrs[$page_name])){
			foreach($this->databese_parametrs[$page_name] as $key => $value){
				$page_parametrs[$key]=get_option($key,$value);
			}
			return $page_parametrs;
		}
		return NULL;
		
	}
	
	/*############################### Database function ########################################*/	
	
	public function save_in_databese(){
		$kk=1;			
		if(isset($_POST['like_box_options_nonce']) && wp_verify_nonce( $_POST['like_box_options_nonce'],'like_box_options_nonce')){
			foreach($this->databese_parametrs[$_POST['curent_page']] as $key => $value){
				if(isset($_POST[$key]))
					update_option($key, sanitize_text_field($_POST[$key]) );
				else{
					$kk=0;
					printf($this->text_parametrs['error_in_saving'],$key);
				}
			}	
		}
		else{		
			die($this->text_parametrs['authorize_problem']);
		}
		if($kk==0){
			exit;
		}
		die($this->text_parametrs['parametrs_sucsses_saved']);
	}
	
    /*############ Main menu Function ##################*/	
	
	public function main_menu_function(){	
		?>
        <script>
        var like_box_ajaxurl="<?php echo esc_url(admin_url( 'admin-ajax.php')); ?>";
		var like_box_plugin_url="<?php echo esc_url($this->plugin_url); ?>";
		var like_box_parametrs_sucsses_saved="<?php echo esc_html($this->text_parametrs['parametrs_sucsses_saved']) ?>";
		var like_box_all_parametrs = <?php echo json_encode($this->databese_parametrs); ?>;
        </script>
		<div class="wpdevart_plugins_header div-for-clear">
			<div class="wpdevart_plugins_get_pro div-for-clear">
				<div class="wpdevart_plugins_get_pro_info">
					<h3>WpDevArt Like Box Premium</h3>
					<p>Powerful and Customizable Like Box</p>
				</div>
					<a target="blank" href="https://wpdevart.com/wordpress-facebook-like-box-plugin/" class="wpdevart_upgrade">Upgrade</a>
			</div>
			<a target="blank" href="<?php echo wpdevart_likebox_support_url; ?>" class="wpdevart_support">Have any Questions? Get a quick support!</a>
		</div>      
	<br>
     
       <div class="wp-table right_margin">
        <table class="wp-list-table widefat fixed posts">
        	<thead>
                <tr>
                    <th>     
                     <h4 class="live_previev">Facebook Popup and Sticky Box Settings</h4>              
                   <span class="save_all_paramss"> <button type="button" id="save_all_parametrs" class="save_all_section_parametrs button button-primary"><span class="save_button_span">Save All Sections</span> <span class="saving_in_progress"> </span><span class="sucsses_save"> </span><span class="error_in_saving"> </span></button></span>
                    </th>
                </tr>
         	</thead>
            <tbody>
            <tr>
            	<td>
                <div id="like_box_page">
    				<div class="left_sections">
						<?php
                       		$this->generete_popup_section($this->generete_parametrs('popup_like_box'));										
                       	?>
                     </div>
    				 <div class="right_sections">
                     <?php
					 		$this->generete_sidbar_slide_section($this->generete_parametrs('sidbar_slide_like_box'));	
                     ?>
                  </div><div style="clear:both"></div>
               </td>
       		</tr>
            </tbody>
            <tfoot>
                <tr>
                    <th>                   
                    	<span class="save_all_paramss"><button type="button" id="save_all_parametrs" class="save_all_section_parametrs button button-primary"><span class="save_button_span">Save All Sections</span> <span class="saving_in_progress"> </span><span class="sucsses_save"> </span><span class="error_in_saving"> </span></button></span>
                    </th>
                </tr>
         	</tfoot>
        </table>
        </div>      
       <?php
	  wp_nonce_field('like_box_options_nonce','like_box_options_nonce');
	}
	
	/*#########################  LIKE BOX POPUP FUNCTION #################################*/
	
	public function generete_popup_section($page_parametrs){
		//for updated parameters
			$jsone_enable_like_box= json_decode(stripslashes($page_parametrs['like_box_enable_like_box']), true); 
			if($jsone_enable_like_box!=NULL){
				if($jsone_enable_like_box['yes']==true){
					$page_parametrs['like_box_enable_like_box']='yes';
				}elseif($jsone_enable_like_box['no']==true){
					$page_parametrs['like_box_enable_like_box']='no';
				}else{
					$page_parametrs['like_box_enable_like_box']='yes';
				}
			}
			
			
			$jsone_like_box_header= json_decode(stripslashes($page_parametrs['like_box_header']), true); 
			if($jsone_like_box_header!=NULL){
				if($jsone_like_box_header['show']==true){
					$page_parametrs['like_box_header']='yes';
				}else{
					$page_parametrs['like_box_header']='yes';
				}
			}

		?>
		<div class="main_parametrs_group_div">
			<div class="head_panel_div">
            	<span class="title_parametrs_image"><img src="<?php echo $this->plugin_url.'images/facebook_popup.png' ?>"></span>
				<span class="title_parametrs_group">Facebook Popup</span>
				<span class="enabled_or_disabled_parametr"></span>
				<span class="open_or_closed"></span>         
			</div>
			<div class="inside_information_div">
				<table class="wp-list-table widefat fixed posts section_parametrs_table">                            
				<tbody> 
               		<tr>
						<td>
							Enable/Disable the Popup <span title="Choose to Enable/Disable the Popup." class="desription_class">?</span>
						</td>
						<td>
							<select id="like_box_enable_like_box">                         
                                <option <?php selected($page_parametrs['like_box_enable_like_box'],'yes') ?> value="yes">Enable</option>
                                <option <?php selected($page_parametrs['like_box_enable_like_box'],'no') ?> value="no">Disable</option>
                        	</select>
						</td>                
					</tr>
					<tr>
						<td>
						<span style="color: #b30404;">Page ID</span> <span title="Type here the Facebook business/fan page URL(without https://www.facebook.com/, if the Facebook business/fan page URL is https://web.facebook.com/AnIMals then type here just the AnIMals)." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_profile_id"   id="like_box_profile_id" value="<?php echo esc_html($page_parametrs['like_box_profile_id']) ?>">
						</td>                
					</tr>
                	<tr>
						<td>
							Show/Hide the like box Popup on these pages  <span class="pro_feature"> (pro)</span><span title="Choose the action to show or hide the like box Popup from the below list." class="desription_class">?</span>
						</td>
						<td>
                           <select class="pro_select">
                                <option value="show">Show on the below selected list</option>
                                <option selected="selected" value="hide">Hide from the below selected list</option>
                        	</select>
                         </td>                
					</tr> 
					<tr>
						<td>
							Select the list <span class="pro_feature"> (pro)</span> <span title="Click on the field and then choose something from the list." class="desription_class">?</span>
						</td>
						<td>
                         	<input class="pro_input" type="text" value="">
                        </td>                
					</tr>                             
					<tr>
						<td>
							Popup display periodicity  <span class="pro_feature"> (pro)</span> <span title="Select the display periodicity of the Popup." class="desription_class">?</span>
						</td>
						<td>
                           <select class="pro_select" id="like_box_popup_show_quantity">
                                <option value="onew_time">Оne Тime</option>
                                <option selected="selected" value="on_refresh">Еvery Тime</option>
                        	</select>
                         </td>                
					</tr>
                    <tr>
						<td>
							Time to display the Popup <span class="pro_feature"> (pro)</span> <span title="Type the time when the Like box popup should appear after the page loads." class="desription_class">?</span>
						</td>
						<td>
							<input class="pro_input" type="text" name="like_box_secont_befor_show"  id="like_box_secont_befor_show" value="1">(Seconds)
						</td>                
					</tr>
                    <tr>
						<td>
							Popup width <span title="Type here the Popup width(px)" class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_width" id="like_box_width" value="<?php echo esc_html($page_parametrs['like_box_width']); ?>">(Px)
						</td>                
					</tr>
                    <tr>
						<td>
							Popup height <span title="Type here the Popup height(px)" class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_height" id="like_box_height" value="<?php echo esc_html($page_parametrs['like_box_height']) ?>">(Px)
						</td>                
					</tr>
                    
                    <tr>
						<td>
							 Popup title <span title="Type here the popup title" class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_popup_title" id="like_box_popup_title" value="<?php echo esc_html($page_parametrs['like_box_popup_title']) ?>">
						</td>                
					</tr>
                     <tr >
                        <td>
                           Popup title color <span title="Set the popup title color" class="desription_class">?</span>
                        </td>
                        <td>
                            <input type="text" class="color_option" id="like_box_popup_title_color" name="like_box_popup_title_color"  value="<?php echo esc_html($page_parametrs['like_box_popup_title_color']); ?>"/>
                         </td>                
                    </tr>
                    <tr>
						<td>
							Popup title Font Family <span title="Select the title Font family" class="desription_class">?</span>
						</td>
						<td>
							<?php $this->create_select_element_for_font('like_box_popup_title_font_famely', esc_html($page_parametrs['like_box_popup_title_font_famely'])) ?>
						</td>                
					</tr>
                    
                    
                    <tr>
                  
						<td>
							Show the border <span class="pro_feature"> (pro)</span>  <span title="Select to show/hide the Facebook like box border." class="desription_class">?</span>
						</td>
						<td>
                           <select class="pro_select" id="like_box_show_border">
                                <option selected="selected" value="show">Show</option>
                                <option  value="hide">Hide</option>
                        	</select>
                         </td>                
					</tr>
                     <tr >
                        <td>
                           Border color <span class="pro_feature"> (pro)</span> <span title="Set the border color of the Facebook Like box." class="desription_class">?</span>
                        </td>
                        <td>
							<div class="wp-picker-container disabled_picker">
								<button type="button" class="button wp-color-result" aria-expanded="false" style="background-color: rgb(255, 255, 255);"><span class="wp-color-result-text">Select Color</span></button>
							</div>
                        </td>                
                    </tr>
                     
                  <tr>
                  
						<td>
							Show the Users Faces <span title="Select to show or hide the Users Faces" class="desription_class">?</span>
						</td>
						<td>                  
                          <select id="like_box_connections">
                                <option <?php selected($page_parametrs['like_box_connections'],'show') ?> value="show">Show</option>
                                <option <?php selected($page_parametrs['like_box_connections'],'hide') ?> value="hide">Hide</option>
                        	</select>
                         </td>                
					</tr>
                     <tr>
						<td>
							Like box header size <span title="Select the Like box header size" class="desription_class">?</span>
						</td>
						<td>
                           <select id="like_box_header">
                                <option <?php selected($page_parametrs['like_box_header'],'small') ?> value="small">Small</option>
                                <option <?php selected($page_parametrs['like_box_header'],'big') ?> value="big">Big</option>
                        	</select>
                         </td>                
					</tr>
                  <tr>
						<td>
							Like box cover photo <span title="Select to sho or hide the Like box cover photo" class="desription_class">?</span>
						</td>
						<td>
                           <select id="like_box_cover_photo">
                                <option <?php selected($page_parametrs['like_box_cover_photo'],'show') ?> value="show">Show</option>
                                <option <?php selected($page_parametrs['like_box_cover_photo'],'hide') ?> value="hide">Hide</option>
                        	</select>
                         </td>                
					</tr>
                    <tr>
						<td>
							Show the latest posts <span title="Select to show or hide the latest posts section from the Facebook like box." class="desription_class">?</span>
						</td>
						<td>
                           <select id="like_box_stream">
                                <option <?php selected($page_parametrs['like_box_stream'],'show') ?> value="show">Show</option>
                                <option <?php selected($page_parametrs['like_box_stream'],'hide') ?> value="hide">Hide</option>
                        	</select>
                         </td>                
					</tr>
                    
                     <tr>
						<td>
							Like box language <span title="Type the Facebook Like box language code(for example en_US). If you left this field blank, the default language will be displayed." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_locale"   id="like_box_locale" value="<?php echo esc_html($page_parametrs['like_box_locale']); ?>">(en_US,de_DE...)
						</td>                
					</tr>
				</tbody>
					<tfoot>
						<tr>
							<th colspan="1" width="100%"><button type="button" id="popup_like_box" class="save_section_parametrs button button-primary"><span class="save_button_span">Save Section</span> <span class="saving_in_progress"> </span><span class="sucsses_save"> </span><span class="error_in_saving"> </span></button><span class="error_massage"> </span></th>
							<th><button type="button" onClick="like_box_setCookie('facbook_like_box_popup','',2); alert('cookie removed')" class="save_button button button-primary" style="float:right;"><span class="save_button_span">Remove cookies</span></button></th>
                        </tr>
					</tfoot>       
				</table>
			</div>     
		</div>        
		<?php	
	}
	/*#########################  STICKY BOX SECTION   #################################*/
	public function generete_sidbar_slide_section($page_parametrs){

		?>
		<div class="main_parametrs_group_div " >
			<div class="head_panel_div">
            	<span class="title_parametrs_image"><img src="<?php echo esc_url($this->plugin_url).'images/facebook_sidebar.png' ?>"></span>
				<span class="title_parametrs_group">Sticky Facebook box</span>
				<span class="enabled_or_disabled_parametr"></span>
				<span class="open_or_closed"></span>         
			</div>
			<div class="inside_information_div">
				<table class="wp-list-table widefat fixed posts section_parametrs_table">                            
				<tbody> 
               		<tr>
						<td>
							Enable/Diasable the Sticky box <span title="Choose to show or hide the Sticky box." class="desription_class">?</span>
						</td>
						<td>
							<select id="like_box_sidebar_slide_mode">
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_mode'],'yes') ?> value="yes">Enable</option>
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_mode'],'no') ?> value="no">Disable</option>
                        	</select>
						</td>                
					</tr>
					<tr>
						<td>
							<span style="color: #b30404;">Page ID</span>  <span title="Type here the Facebook business/fan page URL(without https://www.facebook.com/, if the Facebook business/fan page URL is https://web.facebook.com/AnIMals then type here just the AnIMals)." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_sidebar_slide_profile_id"   id="like_box_sidebar_slide_profile_id" value="<?php echo esc_html($page_parametrs['like_box_sidebar_slide_profile_id']) ?>">
						</td>                
					</tr>
                	<tr>
						<td>
							Show/Hide the Sticky box on these pages <span class="pro_feature"> (pro)</span><span title="Choose the action to show or hide like box sticky box from the below list." class="desription_class">?</span>
						</td>
						<td>
                           <select class="pro_select">
                                <option value="show">Show on the below selected list</option>
                                <option selected="selected" value="hide">Hide from the below selected list</option>
                        	</select>
                         </td>                
					</tr> 
					<tr>
						<td>
							Select the list <span class="pro_feature"> (pro)</span> <span title="Click on the field and then choose something from the list." class="desription_class">?</span>
						</td>
						<td>
                         	<input class="pro_input" type="text" value="">
                        </td>                
					</tr>
                    <tr>
						<td>
							Sticky box position <span class="pro_feature"> (pro)</span> <span title="Select the position of the Sticky box." class="desription_class">?</span>
						</td>
						<td>
							<select class="pro_select" id="like_box_sidebar_slide_position">
                                <option selected="selected" value="left">Left</option>
                                <option value="right">Right</option>
                        	</select>
						</td>                
					</tr>
                    <tr>
						<td>
							Sticky box height <span title="Type the height of the Sticky box." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_sidebar_slide_pntik_height"   id="like_box_sidebar_slide_pntik_height" value="<?php echo esc_html($page_parametrs['like_box_sidebar_slide_pntik_height']) ?>">(px)
						</td>                
					</tr>
                    <tr>
                        <td>
                           Sticky box button bg color  <span class="pro_feature"> (pro)</span> <span title="Set the background color of the Sticky box button." class="desription_class">?</span>
                        </td>
                        <td>
							<div class="wp-picker-container disabled_picker">
								<button type="button" class="button wp-color-result" aria-expanded="false" style="background-color: rgb(62, 89, 165);"><span class="wp-color-result-text">Select Color</span></button>
							</div>                                                
                         </td>                
                    </tr>
                    <tr>
                        <td>
                          Sticky box border color <span class="pro_feature"> (pro)</span> <span title="Set the border color of the Sticky box" class="desription_class">?</span>
                        </td>
                        <td>
                        	<div class="wp-picker-container disabled_picker">
								<button type="button" class="button wp-color-result" aria-expanded="false" style="background-color: rgb(62, 89, 165);"><span class="wp-color-result-text">Select Color</span></button>
							</div>                       
                        </td>                
                    </tr>
                    <tr>
						<td>
							 Sticky box Title <span title="Type here the Sticky box title." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_sidebar_slide_title" id="like_box_sidebar_slide_title" value="<?php echo esc_html($page_parametrs['like_box_sidebar_slide_title']) ?>">
						</td>                
					</tr>
                     <tr>
                        <td>
                            Sticky box Title color <span title="Set the title color of the Sticky box." class="desription_class">?</span>
                        </td>
                        <td>
                            <input type="text" class="color_option" id="like_box_sidebar_slide_title_color" name="like_box_sidebar_slide_title_color"  value="<?php echo esc_html($page_parametrs['like_box_sidebar_slide_title_color']) ?>"/>
                         </td>                
                    </tr>
                    <tr>
						<td>
							Sticky box Title Font family <span title="Select the title font family of the Sticky box." class="desription_class">?</span>
						</td>
						<td>
							<?php $this->create_select_element_for_font('like_box_sidebar_slide_title_font_famely',esc_html($page_parametrs['like_box_sidebar_slide_title_font_famely'])) ?>
						</td>                
					</tr>     
                    <tr>
						<td>
							Like box width <span title=" Type here the Like box width(px)." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_sidebar_slide_width" id="like_box_sidebar_slide_width" value="<?php echo esc_html($page_parametrs['like_box_sidebar_slide_width']); ?>">(Px)
						</td>                
					</tr>                    
                    <tr>
						<td>
							Like box height <span title="Type here the Like box height(px)." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_sidebar_slide_height" id="like_box_sidebar_slide_height" value="<?php echo esc_html($page_parametrs['like_box_sidebar_slide_height']) ?>">(Px)
						</td>                
					</tr>
                     
                  <tr>
						<td>
							Show the Users Faces <span title="Select to show/hide the user faces" class="desription_class">?</span>
						</td>
						<td>
                        	<select id="like_box_sidebar_slide_connections">
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_connections'],'show') ?>  value="show">Show</option>
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_connections'],'hide') ?> value="hide">Hide</option>
                            </select>
                         </td>                
					</tr>
                     <tr>
						<td>
							Like box header size <span title="Select the Like box header size" class="desription_class">?</span>
						</td>
						<td>
                           <select id="like_box_sidebar_slide_header">
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_header'],'small') ?> value="small">Small</option>
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_header'],'big') ?> value="big">Big</option>
                        	</select>
                         </td>                
					</tr>
                   <tr>
						<td>
							Like box cover photo <span title="Select to show/hide the cover photo" class="desription_class">?</span>
						</td>
						<td>
                           <select id="like_box_sidebar_slide_cover_photo">
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_cover_photo'],'show') ?> value="show">Show</option>
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_cover_photo'],'hide') ?> value="hide">Hide</option>
                        	</select>
                         </td>                
					</tr>
                    <tr>
						<td>
							Show the latest posts  <span title="Select to show or hide the latest posts section from the Facebook like box." class="desription_class">?</span>
						</td>
						<td>
                           <select id="like_box_sidebar_slide_stream">
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_stream'],'show') ?> value="show">Show</option>
                                <option <?php selected($page_parametrs['like_box_sidebar_slide_stream'],'hide') ?>  value="hide">Hide</option>
                        	</select>
                         </td>                
					</tr>
                    
                     <tr>
						<td>
							Like box language  <span title="Type the Facebook Like box language code(for example en_US). If you left this field blank, the default language will be displayed." class="desription_class">?</span>
						</td>
						<td>
							<input type="text" name="like_box_sidebar_slide_locale"   id="like_box_sidebar_slide_locale" value="<?php echo esc_html($page_parametrs['like_box_sidebar_slide_locale']); ?>">(en_US,de_DE...)
						</td>                
					</tr>
                   
				</tbody>
					<tfoot>
						<tr>
							<th colspan="2" width="100%"><button type="button" id="sidbar_slide_like_box" class="save_section_parametrs button button-primary"><span class="save_button_span">Save Section</span> <span class="saving_in_progress"> </span><span class="sucsses_save"> </span><span class="error_in_saving"> </span></button><span class="error_massage"> </span></th>
						</tr>
					</tfoot>       
				</table>
			</div>     
		</div>        
		<?php	
	}

	/*######################################### FRONT END ELEMENTS FUNCTION #######################################*/

	private function create_select_element_for_font($select_id='',$curent_font='none'){
		?>
	   <select id="<?php echo $select_id; ?>" name="<?php echo $select_id; ?>">
	   
			<option <?php selected('Arial,Helvetica Neue,Helvetica,sans-serif',$curent_font); ?> value="Arial,Helvetica Neue,Helvetica,sans-serif">Arial *</option>
			<option <?php selected('Arial Black,Arial Bold,Arial,sans-serif',$curent_font); ?> value="Arial Black,Arial Bold,Arial,sans-serif">Arial Black *</option>
			<option <?php selected('Arial Narrow,Arial,Helvetica Neue,Helvetica,sans-serif',$curent_font); ?> value="Arial Narrow,Arial,Helvetica Neue,Helvetica,sans-serif">Arial Narrow *</option>
			<option <?php selected('Courier,Verdana,sans-serif',$curent_font); ?> value="Courier,Verdana,sans-serif">Courier *</option>
			<option <?php selected('Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Georgia,Times New Roman,Times,serif">Georgia *</option>
			<option <?php selected('Times New Roman,Times,Georgia,serif',$curent_font); ?> value="Times New Roman,Times,Georgia,serif">Times New Roman *</option>
			<option <?php selected('Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Arial,sans-serif',$curent_font); ?> value="Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Arial,sans-serif">Trebuchet MS *</option>
			<option <?php selected('Verdana,sans-serif',$curent_font); ?> value="Verdana,sans-serif">Verdana *</option>
			<option <?php selected('American Typewriter,Georgia,serif',$curent_font); ?> value="American Typewriter,Georgia,serif">American Typewriter</option>
			<option <?php selected('Andale Mono,Consolas,Monaco,Courier,Courier New,Verdana,sans-serif',$curent_font); ?> value="Andale Mono,Consolas,Monaco,Courier,Courier New,Verdana,sans-serif">Andale Mono</option>
			<option <?php selected('Baskerville,Times New Roman,Times,serif',$curent_font); ?> value="Baskerville,Times New Roman,Times,serif">Baskerville</option>
			<option <?php selected('Bookman Old Style,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Bookman Old Style,Georgia,Times New Roman,Times,serif">Bookman Old Style</option>
			<option <?php selected('Calibri,Helvetica Neue,Helvetica,Arial,Verdana,sans-serif',$curent_font); ?> value="Calibri,Helvetica Neue,Helvetica,Arial,Verdana,sans-serif">Calibri</option>
			<option <?php selected('Cambria,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Cambria,Georgia,Times New Roman,Times,serif">Cambria</option>
			<option <?php selected('Candara,Verdana,sans-serif',$curent_font); ?> value="Candara,Verdana,sans-serif">Candara</option>
			<option <?php selected('Century Gothic,Apple Gothic,Verdana,sans-serif',$curent_font); ?> value="Century Gothic,Apple Gothic,Verdana,sans-serif">Century Gothic</option>
			<option <?php selected('Century Schoolbook,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Century Schoolbook,Georgia,Times New Roman,Times,serif">Century Schoolbook</option>
			<option <?php selected('Consolas,Andale Mono,Monaco,Courier,Courier New,Verdana,sans-serif',$curent_font); ?> value="Consolas,Andale Mono,Monaco,Courier,Courier New,Verdana,sans-serif">Consolas</option>
			<option <?php selected('Constantia,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Constantia,Georgia,Times New Roman,Times,serif">Constantia</option>
			<option <?php selected('Corbel,Lucida Grande,Lucida Sans Unicode,Arial,sans-serif',$curent_font); ?> value="Corbel,Lucida Grande,Lucida Sans Unicode,Arial,sans-serif">Corbel</option>
			<option <?php selected('Franklin Gothic Medium,Arial,sans-serif',$curent_font); ?> value="Franklin Gothic Medium,Arial,sans-serif">Franklin Gothic Medium</option>
			<option <?php selected('Garamond,Hoefler Text,Times New Roman,Times,serif',$curent_font); ?> value="Garamond,Hoefler Text,Times New Roman,Times,serif">Garamond</option>
			<option <?php selected('Gill Sans MT,Gill Sans,Calibri,Trebuchet MS,sans-serif',$curent_font); ?> value="Gill Sans MT,Gill Sans,Calibri,Trebuchet MS,sans-serif">Gill Sans MT</option>
			<option <?php selected('Helvetica Neue,Helvetica,Arial,sans-serif',$curent_font); ?> value="Helvetica Neue,Helvetica,Arial,sans-serif">Helvetica Neue</option>
			<option <?php selected('Hoefler Text,Garamond,Times New Roman,Times,sans-serif',$curent_font); ?> value="Hoefler Text,Garamond,Times New Roman,Times,sans-serif">Hoefler Text</option>
			<option <?php selected('Lucida Bright,Cambria,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Lucida Bright,Cambria,Georgia,Times New Roman,Times,serif">Lucida Bright</option>
			<option <?php selected('Lucida Grande,Lucida Sans,Lucida Sans Unicode,sans-serif',$curent_font); ?> value="Lucida Grande,Lucida Sans,Lucida Sans Unicode,sans-serif">Lucida Grande</option>
			<option <?php selected('monospace',$curent_font); ?> value="monospace">monospace</option>
			<option <?php selected('Palatino Linotype,Palatino,Georgia,Times New Roman,Times,serif',$curent_font); ?> value="Palatino Linotype,Palatino,Georgia,Times New Roman,Times,serif">Palatino Linotype</option>
			<option <?php selected('Tahoma,Geneva,Verdana,sans-serif',$curent_font); ?> value="Tahoma,Geneva,Verdana,sans-serif">Tahoma</option>
			<option <?php selected('Rockwell, Arial Black, Arial Bold, Arial, sans-serif',$curent_font); ?> value="Rockwell, Arial Black, Arial Bold, Arial, sans-serif">Rockwell</option>
		</select>
		<?php
		}

		public function hire_expert() {
			$plugins_array = array(
				'custom_site_dev' => array(
					'image_url' => $this->plugin_url.'images/hire_expert/1.png',
					'title' => 'Custom WordPress Development',
					'description' => 'Hire a WordPress expert and make any custom development for your WordPress website.',
				),
				'custom_plug_dev' => array(
					'image_url' => $this->plugin_url.'images/hire_expert/2.png',
					'title' => 'WordPress Plugin Development',
					'description' => 'Our developers can create any WordPress plugin from zero. Also, they can customize any plugin and add any functionality.',
				),
				'custom_theme_dev' => array(
					'image_url' => $this->plugin_url.'images/hire_expert/3.png',
					'title' => 'WordPress Theme Development',
					'description' => 'If you need an unique theme or any customizations for a ready theme, then our developers are ready.',
				),
				'custom_theme_inst' => array(
					'image_url' => $this->plugin_url.'images/hire_expert/4.png',
					'title' => 'WordPress Theme Installation and Customization',
					'description' => 'If you need a theme installation and configuration, then just let us know, our experts configure it.',
				),
				'gen_wp_speed' => array(
					'image_url' => $this->plugin_url.'images/hire_expert/5.png',
					'title' => 'General WordPress Support',
					'description' => 'Our developers can provide general support. If you have any problem with your website, then our experts are ready to help.',
				),
				'speed_op' => array(
					'image_url' => $this->plugin_url.'images/hire_expert/6.png',
					'title' => 'WordPress Speed Optimization',
					'description' => 'Hire an expert from WpDevArt and let him take care of your website speed optimization.',
				),
				'mig_serv' => array(
					'image_url' => $this->plugin_url.'images/hire_expert/7.png',
					'title' => 'WordPress Migration Services',
					'description' => 'Our developers can migrate websites from any platform to WordPress.',
				),
				'page_seo' => array(
					'image_url' => $this->plugin_url.'images/hire_expert/8.png',
					'title' => 'WordPress SEO',
					'description' => 'SEO is an important part of any website. Hire an expert and he will organize the SEO of your website.',
				),
			);
			$content = '';
			$content .= '<h1 class="wpda_hire_exp_h1"> Hire an Expert from WpDevArt </h1>';
			$content .= '<div class="hire_expert_main">';
			foreach ($plugins_array as $key => $plugin) {
				$content .= '<div class="wpdevart_hire_main"><a target="_blank" class="wpda_hire_buklet" href="https://wpdevart.com/hire-a-wordpress-developer-online-submit-form/">';
				$content .= '<div class="wpdevart_hire_image"><img src="' . $plugin["image_url"] . '"></div>';
				$content .= '<div class="wpdevart_hire_information">';
				$content .= '<div class="wpdevart_hire_title">' . $plugin["title"] . '</div>';
				$content .= '<p class="wpdevart_hire_description">' . $plugin["description"] . '</p>';
				$content .= '</div></a></div>';
			}
			$content .= '<div><a target="_blank" class="wpda_hire_button" href="https://wpdevart.com/hire-a-wordpress-developer-online-submit-form/">Hire an Expert</a></div>';
			$content .= '</div>';
			echo $content;
		}
	
		public function featured_plugins() {
			$plugins_array=array(
				'gallery_album'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/gallery-album-icon.png',
							'site_url'		=>	'https://wpdevart.com/wordpress-gallery-plugin/',
							'title'			=>	'WordPress Gallery plugin',
							'description'	=>	'The gallery plugin is a useful tool that will help you to create Galleries and Albums. Try our nice Gallery views and awesome animations.'
							),	
				'countdown-extended'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/icon-128x128.png',
							'site_url'		=>	'https://wpdevart.com/wordpress-countdown-extended-version/',
							'title'			=>	'WordPress Countdown Extended',
							'description'	=>	'Countdown Extended is a fresh and extended version of the countdown timer. You can easily create and add countdown timers to your website.'
							),									
				'coming_soon'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/coming_soon.png',
							'site_url'		=>	'https://wpdevart.com/wordpress-coming-soon-plugin/',
							'title'			=>	'Coming soon and Maintenance mode',
							'description'	=>	'Coming soon and Maintenance mode plugin is an awesome tool to show your visitors that you are working on your website to make it better.'
							),
				'chart'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/chart-featured.png',
							'site_url'		=>	'https://wpdevart.com/wordpress-organization-chart-plugin/',
							'title'			=>	'WordPress Organization Chart',
							'description'	=>	'WordPress organization chart plugin is a great tool for adding organizational charts to your WordPress websites.'
							),	
				'Contact forms'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/contact_forms.png',
							'site_url'		=>	'https://wpdevart.com/wordpress-contact-form-plugin/',
							'title'			=>	'Contact Form Builder',
							'description'	=>	'Contact Form Builder plugin is a handy tool for creating different types of contact forms on your WordPress websites.'
							),							
				'Booking Calendar'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/Booking_calendar_featured.png',
							'site_url'		=>	'https://wpdevart.com/wordpress-booking-calendar-plugin/',
							'title'			=>	'WordPress Booking Calendar',
							'description'	=>	'WordPress Booking Calendar plugin is an awesome tool to create a booking system for your website. Create booking calendars in a few minutes.'
							),
				'Pricing Table'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/Pricing-table.png',
							'site_url'		=>	'https://wpdevart.com/wordpress-pricing-table-plugin/',
							'title'			=>	'WordPress Pricing Table',
							'description'	=>	'WordPress Pricing Table plugin is a nice tool for creating beautiful pricing tables. Use WpDevArt pricing table themes and create tables just in a few minutes.'
							),						
				'youtube'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/youtube.png',
							'site_url'		=>	'https://wpdevart.com/wordpress-youtube-embed-plugin/',
							'title'			=>	'WordPress YouTube Embed',
							'description'	=>	'YouTube Embed plugin is a convenient tool for adding videos to your website. Use YouTube Embed plugin for adding YouTube videos in posts/pages, widgets.'
							),
				'facebook-comments'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/facebook-comments-icon.png',
							'site_url'		=>	'https://wpdevart.com/wordpress-facebook-comments-plugin/',
							'title'			=>	'Wpdevart Social comments',
							'description'	=>	'WordPress Facebook comments plugin will help you to display Facebook Comments on your website. You can use Facebook Comments on your pages/posts.'
							),						
				'countdown'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/countdown.jpg',
							'site_url'		=>	'https://wpdevart.com/wordpress-countdown-plugin/',
							'title'			=>	'WordPress Countdown plugin',
							'description'	=>	'WordPress Countdown plugin is a nice tool for creating countdown timers for your website posts/pages and widgets.'
							),
				'lightbox'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/lightbox.png',
							'site_url'		=>	'https://wpdevart.com/wordpress-lightbox-plugin/',
							'title'			=>	'WordPress Lightbox plugin',
							'description'	=>	'WordPress Lightbox Popup is a highly customizable and responsive plugin for displaying images and videos in the popup.'
							),
				'vertical_menu'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/vertical-menu.png',
							'site_url'		=>	'https://wpdevart.com/wordpress-vertical-menu-plugin/',
							'title'			=>	'WordPress Vertical Menu',
							'description'	=>	'WordPress Vertical Menu is a handy tool for adding nice vertical menus. You can add icons for your website vertical menus using our plugin.'
							),
				'duplicate_page'=>array(
							'image_url'		=>	$this->plugin_url.'images/featured_plugins/featured-duplicate.png',
							'site_url'		=>	'https://wpdevart.com/wordpress-duplicate-page-plugin-easily-clone-posts-and-pages/',
							'title'			=>	'WordPress Duplicate page',
							'description'	=>	'Duplicate Page or Post is a great tool that allows duplicating pages and posts. Now you can do it with one click.'
							),
			);
			$html = '';
			$html .= '<h1 class="wpda_featured_plugins_title">Featured Plugins</h1>';
			foreach ($plugins_array as $plugin) {
				$html .= '<div class="featured_plugin_main">';
				$html .= '<div class="featured_plugin_image"><a target="_blank" href="' . $plugin['site_url'] . '"><img src="' . $plugin['image_url'] . '"></a></div>';
				$html .= '<div class="featured_plugin_information">';
				$html .= '<div class="featured_plugin_title">';
				$html .= '<h4><a target="_blank" href="' . $plugin['site_url'] . '">' . $plugin['title'] . '</a></h4>';
				$html .= '</div>';
				$html .= '<p class="featured_plugin_description">' . $plugin['description'] . '</p>';
				$html .= '<a target="_blank" href="' . $plugin['site_url'] . '" class="blue_button">Check The Plugin</a>';
				$html .= '</div>';
				$html .= '<div style="clear:both"></div>';
				$html .= '</div>';
			}
			echo $html;
		}
	
		public function featured_themes() {
			$themes_array = array(
				'tistore' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/tistore.jpg',
					'site_url' => 'https://wpdevart.com/tistore-best-ecommerce-theme-for-wordpress/',
					'title' => 'TiStore',
					'description' => 'TiStore is one of the best eCommerce WordPress themes that is fully integrated with WooCommerce.',
				),
				'megastore' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/megastore.jpg',
					'site_url' => 'https://wpdevart.com/megastore-best-woocommerce-theme-for-wordpress/',
					'title' => 'MegaStore',
					'description' => 'MegaStore is one of the best WooCommerce themes available for WordPress.',
				),
				'jevstore' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/jevstore.jpg',
					'site_url' => 'https://wpdevart.com/jewstore-best-wordpress-jewelry-store-theme/',
					'title' => 'JewStore',
					'description' => 'JewStore is a WordPress WooCommerce theme designed for jewelry stores and blogs.',
				),
				'cakeshop' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/cakeshop.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-cake-shop-theme/',
					'title' => 'Cake Shop',
					'description' => 'WordPress Cake Shop is a multi-purpose WooCommerce-ready theme.',
				),
				'flowershop' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/flowershop.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-flower-shop-theme/',
					'title' => 'Flower Shop',
					'description' => 'WordPress Flower Shop is a responsive and WooCommerce-ready theme developed by our team.',
				),
				'coffeeshop' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/coffeeshop.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-coffee-shop-cafe-theme/',
					'title' => 'Coffee Shop',
					'description' => 'It is a responsive and user-friendly theme designed specifically for coffee shop or cafe websites.',
				),
				'weddingplanner' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/weddingplanner.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-wedding-planner-theme/',
					'title' => 'Wedding Planner',
					'description' => 'Wedding Planner is a responsive WordPress theme that is fully integrated with WooCommerce.',
				),
				'Amberd' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/Amberd.jpg',
					'site_url' => 'https://wpdevart.com/amberd-wordpress-online-store-theme/',
					'title' => 'AmBerd',
					'description' => 'AmBerd has all the necessary features and functionality to create a beautiful WordPress website.',
				),
				'bookshop' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/bookshop.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-book-shop-theme/',
					'title' => 'Book Shop',
					'description' => 'The Book Shop WordPress theme is a fresh and well-designed theme for creating bookstores or book blogs.',
				),
				'ecommercemodernstore' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/ecommercemodernstore.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-ecommerce-modern-store-theme/',
					'title' => 'Ecommerce Modern Store',
					'description' => 'WordPress Ecommerce Modern Store theme is one of the best solutions if you want to create an online store.',
				),
				'electrostore' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/electrostore.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-electronics-store-electro-theme/',
					'title' => 'ElectroStore',
					'description' => 'This is a responsive and WooCommerce-ready electronic store theme.',
				),
				'jewelryshop' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/jewelryshop.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-jewelry-shop-theme/',
					'title' => 'Jewelry Shop',
					'description' => 'WordPress Jewelry Shop theme is designed specifically for jewelry websites, but of course, you can use this theme for other types of websites as well.',
				),
				'fashionshop' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/fashionshop.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-fashion-shop-theme/',
					'title' => 'Fashion Shop',
					'description' => 'The Fashion Shop is one of the best responsive WordPress WooCommerce themes for creating a fashion store website.',
				),
				'barbershop' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/barbershop.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-barbershop-theme/',
					'title' => 'Barbershop',
					'description' => 'WordPress Barbershop is another responsive and functional theme developed by our team.',
				),
				'furniturestore' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/furniturestore.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-furniture-store-theme/',
					'title' => 'Furniture Store',
					'description' => 'This is a great option to quickly create an online store using our theme and the WooCommerce plugin. Our theme is fully integrated with WooCommerce.',
				),
				'clothing' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/clothing.jpg',
					'site_url' => 'https://wpdevart.com/tistore-best-ecommerce-theme-for-wordpress/',
					'title' => 'Clothing',
					'description' => 'The Clothing WordPress theme is one of the best responsive eCommerce themes available for WordPress.',
				),
				'weddingphotography' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/weddingphotography.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-wedding-photography-theme/',
					'title' => 'Wedding Photography',
					'description' => 'WordPress Wedding Photography theme is one of the best themes specially designed for wedding photographers or photography companies.',
				),
				'petshop' => array(
					'image_url' => $this->plugin_url.'images/featured_themes/petshop.jpg',
					'site_url' => 'https://wpdevart.com/wordpress-pet-shop-theme/',
					'title' => 'Pet Shop',
					'description' => 'Pet Shop is a powerful and well-designed WooCommerce WordPress theme.',
				),
	
			);
			$html = '';
			$html .= '<div class="wpdevart_main"><h1 class="wpda_featured_themes_title">Featured Themes</h1>';
	
			$html .= '<div class="div-container">';
			foreach ($themes_array as $theme) {
				$html .= '<div class="theme" data-slug="tistore"><div class="theme-img">';                
				$html .= ' <img src="'.$theme['image_url'].'" alt="' . $theme['title'] . '">';
				$html .= '</div>';
				$html .= '<div class="theme-description">' . $theme['description'] . '</div>';
				$html .= '<div class="theme-name-container">'; 
				$html .= '<h2 class="theme-name">' . $theme['title'] . '</h2>';
				$html .= '<div class="theme-actions">';
				$html .= '<a target="_blank" aria-label="Check theme" class="button button-primary load-customize" href="' . $theme['site_url'] . '">Check Theme</a>';
				$html .= '</div></div></div>';
				
				
			}
			$html .= '</div></div>';
			echo $html;
		}
	
}