
  $(document).ready(function() {
        
        //palitra
        var f = $.farbtastic('#picker');
        var p = $('#picker').css('opacity', 0.25);
        var selected;
        $('.colorwell')
          .each(function () { f.linkTo(this); $(this).css('opacity', 0.75); })
          .focus(function() {
            if (selected) {
              $(selected).css('opacity', 0.75).removeClass('colorwell-selected');
            }
            f.linkTo(this);
            p.css('opacity', 1);
            $(selected = this).css('opacity', 1).addClass('colorwell-selected');
          });
           
           // click 
      $('.farbtastic').click(function(){
        
        var bg = $('.form-item input#color1').val();
        var bg2 = $('.form-item input#color2').val();
        var border_color = $('.form-item input#color3').val();
        var link = $('.form-item input#color4').val();
        var stylenow = $("#BackTop").attr('style');
        
        $("#BackTop").attr('style',''+stylenow+'background: '+bg+';background: -moz-linear-gradient(top, '+bg+' 0%, '+bg2+' 100%);background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,'+bg+'), color-stop(100%,'+bg2+'));background: -webkit-linear-gradient(top, '+bg+' 0%,'+bg2+' 100%);background: -o-linear-gradient(top, '+bg+' 0%,'+bg2+' 100%);background: -ms-linear-gradient(top, '+bg+' 0%,'+bg2+' 100%);background: linear-gradient(to bottom, '+bg+' 0%,'+bg2+' 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="'+bg+'", endColorstr="'+bg2+'",GradientType=0 );');
        
        $("#BackTop").css('border-color',border_color);
        
        $("#BackTop .BackTopText").css('color',link);
        
        $("#BackTop .BackTopText").mouseenter(function(){ 
            var link_hover = $('.form-item input#color5').val();
            $(this).css('color',link_hover);
        }).mouseleave(function(){ 
            $(this).css('color',link);
            });
      
      });
            //end click
      
      
      //change
       $('.form-item  #b_radius').change(function(){
       var br = $(this).val();
       $("#BackTop").css('-moz-border-radius',br+'px').css('-webkit-border-radius',br+'px').css('-khtml-border-radius',br+'px').css('border-radius',br+'px'); 
      });  
      
      $('.form-item #b_size').change(function(){
       var b_size = $(this).val();
       $("#BackTop").css('border-width',b_size+'px'); 
      });  
      
      $('.form-item #b_width').change(function(){
       var b_width = $(this).val();
       $("#BackTop").css('width',b_width+'px'); 
      }); 
      
      $('.form-item #b_height').change(function(){
       var b_height = $(this).val();
       $("#BackTop").css('height',b_height+'px');
        $("#BackTop .BackTopText").css('line-height',b_height+'px');
      }); 
      
       $('.form-item #b_opacity').change(function(){
       var b_opacity = $(this).val();
       $("#BackTop").css('opacity',b_opacity).css('-moz-opacity',b_opacity).css('-khtml-opacity',b_opacity);
      }); 
      
      $('.form-item input#color1').change(function(){
       var bg = $('.form-item input#color1').val();
       $("#BackTop").css('background',bg); 
      });
      
      $('.form-item input#color4').change(function(){
       var link = $('.form-item input#color4').val();
       $("#BackTop .BackTopText").css('color',link); 
      });
      
      $('.form-item #b_fontsize').change(function(){
       var font_size= $('.form-item #b_fontsize').val();
       $("#BackTop .BackTopText").css('font-size',font_size+'px'); 
      });
      
      $('.form-item #b_text').change(function(){
       var b_text = $('.form-item #b_text').val();
       $("#BackTop .BackTopText").html(b_text); 
       
       if( $("#BackTop .BackTopText").html() == "" ){
        $("#BackTop .BackTopText").html('&#9650;');
      }
      
      });
      
      $('.form-item input#color3').change(function(){
       var border = $('.form-item input#color3').val();
       $("#BackTop").css('border-color',border); 
      });
   
   
   // initial
   
       var bg = $('.form-item input#color1').val();
       var bg2 = $('.form-item input#color2').val();
       var border_color = $('.form-item input#color3').val();
       var link = $('.form-item input#color4').val();
       var font_size = $('.form-item #b_fontsize').val();
       var b_height = $('.form-item #b_height').val();
       var b_width = $('.form-item #b_width').val();
       var b_size = $('.form-item #b_size').val();
       var b_radius =$('.form-item  #b_radius').val();
       var b_text= $('.form-item #b_text').val();
       
      
        $("#BackTop").attr('style','background: '+bg+';background: -moz-linear-gradient(top, '+bg+' 0%, '+bg2+' 100%); /* FF3.6+ */background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,'+bg+'), color-stop(100%,'+bg2+')); /* Chrome,Safari4+ */background: -webkit-linear-gradient(top, '+bg+' 0%,'+bg2+' 100%); /* Chrome10+,Safari5.1+ */background: -o-linear-gradient(top, '+bg+' 0%,'+bg2+' 100%); /* Opera 11.10+ */background: -ms-linear-gradient(top, '+bg+' 0%,'+bg2+' 100%); /* IE10+ */background: linear-gradient(to bottom, '+bg+' 0%,'+bg2+' 100%); /* W3C */filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="'+bg+'", endColorstr="'+bg2+'",GradientType=0 ); /* IE6-8 */');
        $("#BackTop").css('border-color',border_color).css('width',b_width+'px').css('height',b_height+'px').css('border-width',b_size+'px');
        $("#BackTop").css('-moz-border-radius',b_radius+'px').css('-webkit-border-radius',b_radius+'px').css('-khtml-border-radius',b_radius+'px').css('border-radius',b_radius+'px'); 
      
        
        
        $("#BackTop .BackTopText").html(b_text).css('color',link).css('line-height',b_height+'px').css('font-size',font_size+'px');
        
         if( $("#BackTop .BackTopText").html() == "" ){
        $("#BackTop .BackTopText").html('&#9650;');
      }
        
        $("#BackTop .BackTopText").mouseenter(function(){
            
            var link_hover = $('.form-item input#color5').val();
            $(this).css('color',link_hover);
        }).mouseleave(function(){ 
            $(this).css('color',link);
            });
        
            
        $("#BackTop").css('opacity',b_opacity).css('-moz-opacity',b_opacity).css('-khtml-opacity',b_opacity);
        
        $("#BackTop").mouseenter(function(){ 
            $(this).css('opacity','1').css('-moz-opacity','1').css('-khtml-opacity','1');
        }).mouseleave(function(){ 
            var b_opacity = $('.form-item #b_opacity').val();
            $(this).css('opacity',b_opacity).css('-moz-opacity',b_opacity).css('-khtml-opacity',b_opacity);
        });
  });
