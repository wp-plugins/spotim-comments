;var SMOT_IM;
(function ($, window, document, undefined) {
     
    'use strict';
    
    SMOT_IM = {
        prefix: 'spot-im',
        //mainprogressbar:'#spot_im_main_progressbar',
        commentprogressbar:'#spot_im_comment_progressbar',
        progressLabel:'.progress-label',
        //mainprogressbarLabel:false,
        commentprogressbarLabel:false,
        active:'.spot_im_active',
        wrapper:'.spot_im_sync_wrapper',
        interval:1000,
        init: function () {
            //main progressbar
            //this.mainprogressbar = $(this.mainprogressbar);
            //this.mainprogressbarLabel = this.mainprogressbar.find(this.progressLabel);
          
            //comment progressbar
            this.commentprogressbar = $(this.commentprogressbar);
            this.commentprogressbarLabel = this.commentprogressbar.find(this.progressLabel);
           
            this.initProgress();
            this.SetProgress('main');
            this.Start();
        },
        Start:function(){
          var $self = this;
          $('#spot_im_start').click(function(e){
                e.preventDefault();
                if(!$(this).hasClass('disabled')){
                    $(this).attr('disabled','disabled').addClass('disabled');
                    $(this).val('Working');
                    $self.AjaxStep($self.CurrentStepUrl());
                }
          });
          $('.spot_im_tabs a').click(function(e){
              e.preventDefault();
          });
        },
        initProgress:function(){
            var $self = this;
            /*
            $self.mainprogressbar.progressbar({
               value: false,
               create:function(){
                    $self.SetProgress('main'); 
               },
               change: function() {
                 $self.mainprogressbarLabel.text( $self.mainprogressbar.progressbar( "value" ) + "%" );
               },
               complete: function() {
                 $self.mainprogressbarLabel.text( "Complete!" );
               }
            });
            */
            
            $self.commentprogressbar.progressbar({
               value: 0,
               create:function(){
                 $self.setPrepareProgress(0);
               },
               change: function() {
                 $self.commentprogressbarLabel.text( $self.commentprogressbar.progressbar( "value" ) + "%" );
               },
               complete: function() {
                 $self.commentprogressbarLabel.text( "Complete!" );
               }
            });
            
            
        },
        SetProgress:function($case,$procent){
            if($case=='main'){
                var $step = this.GetActiveStep();
                /*$procent = ($step.index()+1)*parseInt(100/$(this.wrapper).find('.spot_im_tabs li').length);
                this.mainprogressbar.progressbar( "value", $procent);
                */
               $('#spot_im_steps b').html($step.index()+1);
            }
            else if($case=='comment'){
                 this.commentprogressbar.progressbar( "value", $procent);
            }
        },
        SetNextStep:function(){
          var $next =  this.GetNextStep();
          $(this.wrapper).find(this.active).removeClass(this.active.replace('.',''));
          $next.addClass(this.active.replace('.',''));
          this.SetProgress('main');
        },
        GetNextStep:function(){
            var $step = this.GetActiveStep();
            return $step.next();
        },
        GetActiveStep:function(){
            return $(this.wrapper).find(this.active);
        },
        CurrentStepUrl:function(){
            var $step = this.GetActiveStep();
            return $step.children('a').attr('href');
        },
        getProccent:function($total,$value){
            $total = parseInt($total);
            $value = parseInt($value);
            return $value?parseInt(($value*100)/$total):0;
        },
        setPrepareProgress:function($comment_count){
            var $_comment = parseInt($('.spot_im_comment_current>b').text())+$comment_count;
            if($_comment>0){ 
                $('.spot_im_comment_current>b').html($_comment);
                var $total_comment = parseInt($('.spot_im_comment_current>.spot_im_total').text());
                this.SetProgress('comment',this.getProccent($total_comment, $_comment));
            }
        },
        AjaxStep:function($url){
            var $self = this;
            $.ajax({
               url: $url,
               data:$('#spot_im_form').serialize(),
               dataType:'json',
               type:'POST',
               success:function($resp){
                   if($resp){
                       
                       if($resp.error){
                           alert($resp.error);
                       }
                       else if($resp.status=='repeat' || $resp.status=='next'){
                           if($resp.status=='next'){
                               $self.SetNextStep();
                               $url = $self.CurrentStepUrl();
                           }
                           else{
                              $self.setPrepareProgress($resp.comment_count);
                           }
                           setTimeout(function(){$self.AjaxStep($url);},$self.interval);
                       }
                       else if($resp.status=='finish'){
                            $('#spot_im_start').remove();
                            $('#spot_im_export_data').val($resp.data);
                            if($resp.zip){
                                window.location.href = $resp.zip;
                            }
                       }
                   }
               }
            });
        },
    };

    $(document).ready(function(){
        SMOT_IM.init();
        $('#spot-im-spot-form').submit(function(e){
            
            var $spot_id = $(this).find('input[type="text"]');
            var $old = $.trim($spot_id.prev('input').val());
            var $new = $.trim($spot_id.val());
            if(!$new){
                alert(spot_im_trans.spot_im_req);
                e.preventDefault();
            }
            else if(($new==$old) || ($new!=$old && confirm(spot_im_trans.change_spot_im))){
               return true;
            }  
           e.preventDefault();
        });
    });
}(jQuery, window, document));