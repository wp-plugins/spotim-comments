<?php

/**
 * Class to work with comments
 *
 * @link       https://www.spot.im/
 * @since      1.0.0
 *
 * @package    SPOT_IM
 * @subpackage SPOT_IM/includes
 */

class SPOT_IM_Comments {

    const EXPORT_COMMMENT_COUNT = 3000;//only in demo,in the next versions will be option in admin panel
        
    /**
    * Get Total post count 
    *
    * @since 1.0.0
    * @return int
    */
     public static function get_post_total(){
        add_filter( 'posts_where', array(__CLASS__,'filter_comment_count') );
        $query  = new WP_Query(array(
            'order'=>'ASC',
            'orderby'=>'ID',
            'posts_per_page'=>1,
            'ignore_sticky_posts'=>false,
            'posts_groupby'=>'ID',
            'post_type'=>'any',
            'fields'=>array('ID'),
            'post_status'=>array('publish','private','pending','future')
        ));
        wp_reset_postdata();
        return $query->found_posts;
     }
     
     
    
    /**
    * Get Total comments count 
    *
    * @since 1.0.0
    * @param  int  $post_id optionaly 
    * @return int
    */
    public static function get_comment_total($post_id=0){
        return wp_count_comments($post_id)->approved;
     }
     
    /**
    * Remove current filter and return string to change hook post_where
    *
    * @since 1.0.0
    * @param  string  $where 
    * @return string
    */
     public static function filter_comment_count($where){
         remove_filter( current_filter(), __FUNCTION__ );
         return $where.' AND comment_count > 0';
     }

    
    /**
    * Get Comments
    *
    * @since 1.0.0
    * @param  int  $offset 
    * @return array
    */
    public static function get_comments($offset){
       $comments_list = array();
       $comments = get_comments(array(
            'status'=>'approve',
            'orderby'=>array('comment_post_ID','comment_parent','comment_date_gmt'),
            'order'=>'ASC',
            'offset'=>$offset,
            'number'=>self::EXPORT_COMMMENT_COUNT
        ));
       if(!empty($comments)){
           /*
            To close the the last post's comments tree
            *             */
           foreach($comments as $com){
               $comments_list[$com->comment_post_ID][$com->comment_ID] = $com;
           }
           $last_post_id = $com->comment_post_ID;
           $comments = get_comments(array( 
                'status'=>'approve',
                'post_id'=>$last_post_id,
                'orderby'=>array('comment_post_ID','comment_parent','comment_date_gmt'),
                'order'=>'ASC'
            ));
            if(!empty($comments)){
                 foreach($comments as $com){
                    $comments_list[$com->comment_post_ID][$com->comment_ID] = $com;
                }
            }
       }
       return $comments_list;
    }
    
}
