<?php

/**
 * The plugin options management class
 *
 * @link       https://www.spot.im/
 * @since      1.0.0
 *
 * @package    SPOT_IM
 * @subpackage SPOT_IM/includes

/**
 * The plugin options helper class
 */
class SPOT_IM_Options {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

        /**
	 * The owner key 
	 *
	 * @since    1.0.0
	 * @access   private
	 */
        private $blog_owner_key;

        /**
	 * The step key 
	 *
	 * @since    1.0.0
	 * @access   private
	 */
        
	private $blog_step_key;
        
        /**
	 * The spot_id key 
	 *
	 * @since    1.0.0
	 * @access   private
	 */
        
        private $spot_id_key;
        
       
        /**
	 * The exported data key 
	 *
	 * @since    1.0.0
	 * @access   private
	 */
        
        private $export_data_key;
        
        /**
	 * The exported date count key 
	 *
	 * @since    1.0.0
	 * @access   private
	 */
        
        private $export_comment_count_key;
        

        /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
            
            $this->plugin_name              = $plugin_name;
            $this->version                  = $version;
            $this->blog_owner_key           = $this->plugin_name.'-owner';
            $this->blog_step_key            = $this->plugin_name.'-step';
            $this->spot_id_key              = $this->plugin_name.'-spot';
            $this->export_data_key          = $this->plugin_name.'-export';
            $this->export_comment_count_key = $this->plugin_name.'-comment-count';
	}


        /**
	 * Set the Owner
	 *
	 * @since 1.0.0
         * @param  int  $owner 
	 * @return boolean
	 */
        
        public function set_owner($owner){
            return update_option($this->blog_owner_key, $owner);
        }
        
         /**
	 * Get the Owner
	 *
	 * @since 1.0.0
	 *
	 * @return int or boolean 
	 */
        
        public function get_owner(){
           return get_option($this->blog_owner_key);
        }
        
        /**
	 * Set the step in Synchronize page
	 *
	 * @since 1.0.0
	 * @param  int  $step
	 * @return boolean
	 */
        
        public function set_step($step){
           return update_option($this->blog_step_key,intval($step));
        }
        
         /**
	 * Get the step in Synchronize page
	 *
	 * @since 1.0.0
	 *
	 * @return int or boolean 
	 */
        
        public function get_step(){
           return get_option($this->blog_step_key);
        }
        
        /**
	 * Set spot_id
	 *
	 * @since 1.0.0
	 * @param  string  $spot_id
	 * @return boolean
	 */
        
        public function set_spot_id($spot_id){
           return update_option($this->spot_id_key,$spot_id);
        }
        
         /**
	 * Get spot_id
	 *
	 * @since 1.0.0
	 *
	 * @return string or boolean 
	 */
        
        public function get_spot_id(){
           return get_option($this->spot_id_key);
        }
        
         /**
	 * Get spot_id_key
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
        
        public function get_spot_id_key(){
           return $this->spot_id_key;
        }
        
       
         /**
	 * Set exported comment count
	 *
	 * @since 1.0.0
	 * @param  int  $count
	 * @return boolean
	 */
        
        public function set_export_comment_count($count){
           return update_option($this->export_comment_count_key,$count);
        }
        
         /**
	 * Get exported comment count
	 *
	 * @since 1.0.0
	 *
	 * @return int or boolean 
	 */
        
        public function get_export_comment_count(){
           return get_option($this->export_comment_count_key);
        }
        
        
        /**
	 * Set exported data
	 *
	 * @since 1.0.0
	 * @param  array  $data
	 * @return boolean
	 */
        
        public function set_export_data($data){
           return  update_option($this->export_data_key,$data);
        }
        
         /**
	 * Get exported data
	 *
	 * @since 1.0.0
	 *
	 * @return mixed 
	 */
        
        public function get_export_data(){
           return get_option($this->export_data_key);
        }
        
        /**
	 * Owner setup handler
	 *
	 * @since 1.0.0
	 */
        public function set_spot_onwer(){
            if(isset($_POST['owner']) && is_numeric($_POST['owner'])){
                $owner = intval($_POST['owner']);
                $user = get_userdata($owner);
                if(!$user){
                    die(json_encode(array('error'=>__("User doens't exists",'spot-im'))));
                }
                else{
                    if(!in_array('administrator',$user->roles)){
                         die(json_encode(array('error'=>__("User must be administrator",'spot-im'))));
                    }
                    if($this->set_owner($owner)){
                        $this->set_step(2);
                    }
                    die(json_encode(array('status'=>'next')));
                }
            }
        }

        
        
	/**
	 * Prepare data handler
	 *
	 * @since 1.0.0
         * Get not exported comments and add them in array to export
	 */
        
       public function prepare(){
            $data = $this->get_export_data();
            if(!$data){
                $data = array();
                $owner_id = $this->get_owner();
                $owner = get_user_by('id',$owner_id);
                $data['blog_name'] = get_option('blogname');
                $data['blog_owner_name'] = $owner->data->display_name;
                $data['blog_owner_email'] = $owner->data->user_email;
                $data['conversations'] = array();
                $count = 0;
            }
            else{
                $data = json_decode($data,TRUE);
                $count = $this->get_export_comment_count();
            }
             $comments_summ = 0;
             $comments = SPOT_IM_Comments::get_comments($count);       

             if(!empty($comments)){
                
                foreach($comments as $post_id=>$com){
                    $comments_messages = $comments_user = $comments_parent = array();
                    $tree = self::get_comment_tree($comments[$post_id],$comments_messages,$comments_user, $comments_parent);
                    if(empty($comments_parent)){
                        continue;
                    }
                   
                    $site_url = get_permalink($post_id);
                    if(!$site_url){
                        continue;
                    }
                    $data['conversations'][$post_id] = array();
                    $data['conversations'][$post_id]['site_url'] = $site_url;
                    $data['conversations'][$post_id]['comments_ids'] = $comments_parent;
                    $data['conversations'][$post_id]['tree'] = $tree;
                    $data['conversations'][$post_id]['messages'] = $comments_messages;
                    $data['conversations'][$post_id]['users'] = $comments_user['comments'];
                    
                    $comments_summ+=count($comments_messages);
                   unset($comments_parent,$tree,$comments_messages);
                    if(!empty($comments_user['users'])){
                        $wp_user_query = new WP_User_Query( array('orderby'=>'ID', 'order'=>'ASC', 'include' => $comments_user['users'],'fields'=>array('ID','user_email','user_login','display_name') ) );
                        $users = $wp_user_query->get_results();
                        $user_id_email = array();
                        /*check if there are users dont`t exist because wp doesn't remove comments when user is removed*/
                        if(!empty($users)){
                           foreach($users as $user){
                               $data['conversations'][$post_id]['users'][$user->user_email] = array(
                                   'user_name'=>$user->user_login,
                                   'display_name'=>$user->display_name
                               );
                               $user_id_email[$user->user_email] = 1;
                           }
                           foreach ($comments_user['comments'] as $email=>$cuser){//check if users doesn't exists
                                if(!isset($user_id_email[$email])){
                                    foreach($data['conversations'][$post_id]['messages'] as $mkey=>&$msg){
                                        if(!$msg['anonymous'] && $msg['user_id']==$email){
                                            $msg['anonymous'] = TRUE;
                                        }
                                    }
                                }
                            }
                        }
                        
                    }
                    unset($comments_user);
                }
                usleep(1000);
                $args = array('status'=>'repeat');
                $args['comment_count'] = 0;
    
                $data = json_encode($data); 
                if($comments_summ>0 && $this->set_export_data($data)){
                    $count = $count+$comments_summ;
                    $this->set_export_comment_count($count);
                    $args['comment_count'] = $comments_summ;
                }
                elseif($comments_summ==0){
                    foreach ($comments as $com){
                        $comments_summ+=count($com);
                    }
                    $count = $count+$comments_summ;
                    $this->set_export_comment_count($count);
                    $args['comment_count'] = $comments_summ;
                }
                if($count>15000){
                     $this->set_step(3);
                     die(json_encode(array('status'=>'next')));
                }
                else{
                    die(json_encode($args));
                }
                   
            }
            else{
                $this->set_step(3);
                die(json_encode(array('status'=>'next')));
            }
        }
        
	/**
	 * Set post's comments structure as satisfaction by spotim api
	 *
	 * @since 1.0.0
         * @param  array  $comments
         * @param  array  $messages
         * @param  array  $user
         * @param  array  $parents
         * @return array 
	 */
        public static function get_comment_tree(array $comments,array &$messages=array(), array &$user=array(), array &$parents=array()){
            $user['comments'] =  $user['users'] = $comments_list = array();
            
            foreach ($comments as $com) {
                if($com->comment_parent!=0){
                    if(!isset($comments_list[$com->comment_parent])){
                        $comments_list[$com->comment_parent] = array();
                    }
                    $comments_list[$com->comment_parent][] = $com->comment_ID;
                }
                else{
                    $comments_list[$com->comment_ID] = array();
                    $parents[] = $com->comment_ID;
                }
                $messages[$com->comment_ID] = array(
                    'content'=>$com->comment_content,
                    'written_at'=>strtotime($com->comment_date_gmt),
                    'user_id'=>$com->comment_author_email
                );
                if($com->user_id>0){
                    $messages[$com->comment_ID]['anonymous'] = FALSE;
                    $user['users'][] = $com->user_id;
                }
                else{
                    $messages[$com->comment_ID]['anonymous'] = TRUE;
                }
                if(!isset($user['comments'][$com->comment_author_email])){
                    $user['comments'][$com->comment_author_email]['display_name'] = $com->comment_author;
                }
            }

            return $comments_list;
        }
        /**
	 * Export data handler
	 *
	 * @since 1.0.0
         * Demo code will be changed when spotim api will be completed and send the data spotm server
	 */
        public function export(){
            $this->set_step(4);
            die(json_encode(array('status'=>'next')));
        }
        
	 /**
	 * Finish export data handler
	 *
	 * @since 1.0.0
         * In Demo we give user the ziped js file
	 */
       public function finish(){
           $data = $this->get_export_data();
           $create = false;
           $dir = plugin_dir_path(dirname(__FILE__));
           $file = $dir.'sample-data/export.json'; 
           if(file_exists($file) && is_writable($file)){
                $handler = fopen( $file, "w+" );  
                if($handler){
                    if(fwrite( $handler, $data )!==false){
                        fclose($handler);
                        $name = strtolower(esc_html(get_option('blogname')));
                        $name = preg_replace('/\s+/','_',$name);
                        $filename = 'sample-data/'.$name.'_'.date('Y_m_d').'.zip';
                        $filename = str_replace(array('___','__','|','&amp;'),'_',$filename);
                        $filename = str_replace(array('___','__'),'_',$filename);
                        $zip_folder = $dir.$filename;
                        $z = fopen($zip_folder,'w+');
                        fclose($z);
                        if(self::create_zip(array($file),$zip_folder)){
                            $create = TRUE;
                        }
                    }
                    else{
                        fclose($handler);
                    }
               }
           }
            $this->set_step(false);
            $this->set_export_comment_count(0);
            $this->set_export_data(false);
            $filename = $create?plugin_dir_url(dirname(__FILE__)).$filename:'';
            die(json_encode(array('status'=>'finish','data'=>$data,'zip'=>$filename)));
        }
        
	/**
	 * Create zip
	 *
	 * @since 1.0.0
         * @param  array  $files
         * @param  string  $destination
         * @param  boolean  $overwrite
	 * @return boolean
	 */
        
        public static function create_zip($files = array(),$destination = '',$overwrite = true) {
            //if the zip file already exists and overwrite is false, return false
            if(file_exists($destination) && !$overwrite) { return false; }
            //vars
            $valid_files = array();
            //if files were passed in...
            if(is_array($files)) {
                //cycle through each file
                foreach($files as $file) {
                    //make sure the file exists
                    if(file_exists($file)) {
                        $valid_files[] = $file;
                    }
                }
            }
            //if we have good files...
            if(count($valid_files)) {
                //create the archive
                $zip = new ZipArchive();
                if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                    return false;
                }
                //add the files
                foreach($valid_files as $file) {
                    $pathinfo = pathinfo($file);
                    $zip->addFile($file,$pathinfo['filename'].'.'.$pathinfo['extension']);
                }
                //debug
                //echo 'The zip archive contains ',$zip-&gt;numFiles,' files with a status of ',$zip-&gt;status;

                //close the zip -- done!
                $zip->close();

                //check to make sure the file exists
                return file_exists($destination);
            }
            else
            {
                return false;
            }
        }
}