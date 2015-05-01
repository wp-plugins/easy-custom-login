<?php
if ( !class_exists( 'CustomLoginStyle' ) ) {

    class CustomLoginStyle extends EasyCustomLogin {

        public $plugin_url;
        public $plugin_dir;
        public $version;
        public $options;

        public function __construct() {
            global $easyCustomLogin;
            //parent::__construct();
            if ( $easyCustomLogin ) {
                $this->plugin_dir = $easyCustomLogin->plugin_dir;
                $this->plugin_url = $easyCustomLogin->plugin_url;
                $this->version = $easyCustomLogin->version;
                $this->options = $easyCustomLogin->options;
            }

            add_filter( 'ecl_form', array($this, 'login_style_form'), 13 );
            add_action( 'login_head', array($this, 'reset_remember_option'), 99 );
            add_action( 'login_form', array($this, 'start_login_form_cache'), 99 );
            add_filter( 'gettext', array($this, 'disable_password_reset') );
            add_filter( 'login_message', array($this, 'custom_login_message') );
            add_filter( 'login_headerurl', array($this, 'custom_login_header_url') );
            add_filter( 'login_redirect', array($this, 'custom_login_redirect'), 10, 3 );
            add_filter( 'logout_url', array($this, 'custom_logout_url'), 10, 2 );
            //var_dump( $this->options );
        }

        public function disable_password_reset( $text ) {
            if ( isset( $this->options['lost_pw'] ) && $this->options['lost_pw'] == 1 ) {
                if ( $text == 'Lost your password?' ) {
                    $text = '';
                }
            }

            return $text;
        }

        public function reset_remember_option() {
            if ( isset( $this->options['rem_me'] ) && $this->options['rem_me'] == 1 ) {
                if ( isset( $_POST['rememberme'] ) ) {
                    unset( $_POST['rememberme'] );
                }
            }
        }

        public function start_login_form_cache() {
            ob_start( array($this, 'process_login_form_cache') );
        }

        public function process_login_form_cache( $content ) {
            if(isset($this->options['rem_me']) && $this->options['rem_me'] != 0){
                $content = preg_replace( '/<p class="forgetmenot">(.*)<\/p>/', '', $content );
            }

            return $content;
        }

        public function custom_login_message( $message ) {
            return isset( $this->options['login_msg'] ) && $this->options['login_msg'] != '' ? '<p class="login_msg">' . $this->options['login_msg'] . '</p><br><br>' : $message;
        }

        public function custom_login_header_url( $url ) {
            if ( isset( $this->options['login_image_link'] ) ) {
                if ( $this->options['login_image_link'] == 'site_url' ) {
                    return home_url();
                } else {
                    return $this->options['login_ext_url'];
                }
            }
            return $url;
        }

        public function custom_login_redirect( $redirect_to, $request, $user ) {
            return isset( $this->options['login_redirect'] ) && $this->options['login_redirect'] != '' ? $this->options['login_redirect'] : $redirect_to;
        }

        public function custom_logout_url( $logout_url, $redirect ) {
            return isset( $this->options['logout_redirect'] ) && $this->options['logout_redirect'] != '' ? $logout_url . '&redirect_to=' . $this->options['logout_redirect'] : $logout_url . '&redirect_to=' . $redirect;
        }

        public function login_style_form( $form ) {
            //var_dump($this->options);
            ob_start();
            ?>
            <div class="postbox dg_ap_box">
                <h3 class="hndle"><span><?php _e( 'Login Page Settings', 'ecl' ) ?></span></h3>
                <div class="inside ecl">
                    <div style="width:100%; padding-top: 10px;">
                        <strong><?php _e( 'Select login logo', 'ecl' ) ?></strong><br>
                        <input class="media_url" type="text" name="ecl[login_logo]" size="60" value="<?php echo isset( $this->options['login_logo'] ) ? $this->options['login_logo'] : ''; ?>">
                        <a href="#" class="media_url_btn header_logo_upload button button-primary"><i class="fa fa-upload"></i> <?php _e( 'Upload', 'ecl' ) ?></a>
                        <br><br>
                        <?php (!isset( $this->options['login_logo'] ) || $this->options['login_logo'] == '' ) ? $style = 'display:none' : $style = ''; ?>
                        <img style="<?php echo $style; ?>" class="media_url_img header_logo" src="<?php echo isset( $this->options['login_logo'] ) ? $this->options['login_logo'] : ''; ?>" height="100" width="100"/>
                    </div>
                    <div class="clear"></div>
                    <div style="width:100%; padding-top: 10px;">
                        <strong><?php _e( 'Select Background Image<br> (To use background image, clear the field for background color)', 'ecl' ) ?></strong><br>
                        <input class="media_url" type="text" name="ecl[login_bg_img]" size="60" value="<?php echo isset( $this->options['login_bg_img'] ) ? $this->options['login_bg_img'] : ''; ?>">
                        <a href="#" class="media_url_btn header_logo_upload button button-primary"><i class="fa fa-upload"></i> <?php _e( 'Upload', 'ecl' ) ?></a>
                        <br><br>
                        <?php (!isset( $this->options['login_bg_img'] ) || $this->options['login_bg_img'] == '' ) ? $style2 = 'display:none' : $style2 = ''; ?>
                        <img style="<?php echo $style2 ?>" class="media_url_img header_logo" src="<?php echo isset( $this->options['login_bg_img'] ) ? $this->options['login_bg_img'] : ''; ?>" height="100" width="100"/>
                    </div>
                    <div class="clear"></div>
                    <div style="width:100%; padding-top: 10px;">
                        <strong><?php _e( 'Select Background Color<br> (Background color will take precendence over background image)', 'ecl' ) ?></strong><br>
                        <input class="my-color-field" type="text" name="ecl[login_bg_col]" size="60" value="<?php echo isset( $this->options['login_bg_col'] ) ? $this->options['login_bg_col'] : ''; ?>">
                    </div>
                    <div class="clear"></div>
                    <div style="width:100%; padding-top: 10px;">
                        <div style="width: 33%; float: left;">
                            <strong><?php _e( 'Select Font Color', 'ecl' ) ?></strong><br>
                            <input class="my-color-field" type="text" name="ecl[login_font_col]" size="60" value="<?php echo isset( $this->options['login_font_col'] ) ? $this->options['login_font_col'] : ''; ?>">
                        </div>
                        <div style="width: 50%; float: left;">
                            <strong><?php _e( 'Select Form Background Color', 'ecl' ) ?></strong><br>
                            <input class="my-color-field" type="text" name="ecl[login_form_bg_col]" size="60" value="<?php echo isset( $this->options['login_form_bg_col'] ) ? $this->options['login_form_bg_col'] : ''; ?>">
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div style="width:100%; padding-top: 10px;">
                        <div style="width: 33%; float: left;">
                            <strong><?php _e( 'Remove Remember Me', 'ecl' ) ?></strong><br>
                            <input id="rem_on" <?php echo isset( $this->options['rem_me'] ) && $this->options['rem_me'] == 1 ? 'checked="checked"' : '' ?> type="radio" name="ecl[rem_me]" value="1" class="toggle-radio" /> <label for="rem_on"><?php _e( 'Yes', 'ecl' ) ?></label>
                            <input id="rem_off" <?php echo isset( $this->options['rem_me'] ) ? ($this->options['rem_me'] == 0 ? 'checked="checked"' : '') : 'checked="checked"'; ?> type="radio" name="ecl[rem_me]" value="0" class="toggle-radio" /> <label for="rem_off"><?php _e( 'No', 'ecl' ) ?></label>
                        </div>
                        <div style="width: 33%; float: left;">
                            <strong><?php _e( 'Remove Lost Password', 'ecl' ) ?></strong><br>
                            <input id="lost_on" <?php echo isset( $this->options['lost_pw'] ) && $this->options['lost_pw'] == 1 ? 'checked="checked"' : '' ?> type="radio" name="ecl[lost_pw]" value="1" class="toggle-radio" /> <label for="lost_on"><?php _e( 'Yes', 'ecl' ) ?></label>
                            <input id="lost_off" <?php echo isset( $this->options['lost_pw'] ) ? ($this->options['lost_pw'] == 0 ? 'checked="checked"' : '') : 'checked="checked"'; ?> type="radio" name="ecl[lost_pw]" value="0" class="toggle-radio" /> <label for="lost_off"><?php _e( 'No', 'ecl' ) ?></label>
                        </div>
                        <div style="width: 33%; float: left;">
                            <strong><?php _e( 'Remove Back to... Link', 'ecl' ) ?></strong><br>
                            <input id="back_on" <?php echo isset( $this->options['back_to'] ) && $this->options['back_to'] == 1 ? 'checked="checked"' : '' ?> type="radio" name="ecl[back_to]" value="1" class="toggle-radio" /> <label for="back_on"><?php _e( 'Yes', 'ecl' ) ?></label>
                            <input id="back_off" <?php echo isset( $this->options['back_to'] ) ? ( $this->options['back_to'] == 0 ? 'checked="checked"' : '' ) : 'checked="checked"'; ?> type="radio" name="ecl[back_to]" value="0" class="toggle-radio" /> <label for="back_off"><?php _e( 'No', 'ecl' ) ?></label>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div style="width:100%; padding-top: 10px;">
                        <strong><?php _e( 'Select login image link URL', 'ecl' ) ?></strong><br>
                        <input id="site_url" <?php echo isset( $this->options['login_image_link'] ) ? ( $this->options['login_image_link'] == 'site_url' ? 'checked="checked"' : '' ) : 'checked="checked"'; ?> type="radio" name="ecl[login_image_link]" value="site_url" class="toggle-radio" /> <label for="site_url"><?php _e( 'Site URL', 'ecl' ) ?></label>
                        <input id="ext_url" <?php echo isset( $this->options['login_image_link'] ) && $this->options['login_image_link'] == 'ext_url' ? 'checked="checked"' : ''; ?> id="ext_url" type="radio" name="ecl[login_image_link]" value="ext_url" class="toggle-radio" /> <label for="ext_url"><?php _e( 'External URL', 'ecl' ) ?></label><br><br>
                        <input id="ext_url_link" disabled="disabled" value="<?php echo isset( $this->options['login_ext_url'] ) ? $this->options['login_ext_url'] : '' ?>" type="text" name="ecl[login_ext_url]" size="60" />
                    </div>
                    <div class="clear"></div>
                    <div style="width:100%; padding-top: 10px;">
                        <strong><?php _e( 'Login redirect URL', 'ecl' ) ?></strong><br>
                        <input type="text" name="ecl[login_redirect]" value="<?php echo isset( $this->options['login_redirect'] ) ? $this->options['login_redirect'] : '' ?>" size="60" />
                    </div>
                    <div class="clear"></div>
                    <div style="width:100%; padding-top: 10px;">
                        <strong><?php _e( 'Logout redirect URL', 'ecl' ) ?></strong><br>
                        <input type="text" name="ecl[logout_redirect]" value="<?php echo isset( $this->options['logout_redirect'] ) ? $this->options['logout_redirect'] : '' ?>" size="60" />
                    </div>
                    <div class="clear"></div>
                    <div style="width:100%; padding-top: 10px;">
                        <strong><?php _e( 'Add messagae over the login box', 'ecl' ) ?></strong><br>
                        <textarea class="widget_content" name="ecl[login_msg]"><?php echo isset( $this->options['login_msg'] ) ? $this->options['login_msg'] : '' ?></textarea>
                    </div>
                </div>
            </div>
            <?php
            $output = $form . ob_get_contents();
            ob_end_clean();
            return $output;
        }

    }

    new CustomLoginStyle();
}
