$(".ui.dropdown").dropdown("set selected", "1"); 
//객관식으로 무조건 설정

 $('.item-opt-select').dropdown({ 
    // you can use any ui transition
     transition: 'drop',
     onChange:function(val){

     survey.optChange(this, val);
      }
   });
//dropdown이용하기
function selectDropDown(e){

 $(e).parents(".item-group").find(".dropdown").dropdown();

}


$(".item-container").on("focus",".item-group .item-opt-select, input[name='subject[]'], input[name='item_opt_input[]']",function(){

  $(".item-container .item-group").removeClass("active");
  $(this).parents(".item-group").addClass("active");
}); //focus에 active주기


$(".item-container").on("change","input[name='true_or_not[]']",function(){
  $(".item-container .item-group").removeClass("active");
  $(this).parents(".item-group").addClass("active");
});


var survey = new function() {
  

    this.opt = $("input[name='opt_select[]']").val();
  
    this.itemSave = function(e){
      $question = "";
      $type="";
      $require = "";
      $(".item-container .item-group").each(function(i){

        //console.log($(this).find("input[name='true_or_not[]']").val());
        if($(".item-container .item-group").length==i+1){
          $question += $(this).find("input[name='subject[]']").val();
          $type += $(this).find("input[name='opt_select[]']").val();

          if($(this).find("input[name='true_or_not[]']").is(":checked")){
            $require +=$(this).find("input[name='true_or_not[]']").val();
          }else{
            $require +="0";
          }

        }else{
          $question += $(this).find("input[name='subject[]']").val()+";||;";
          $type += $(this).find("input[name='opt_select[]']").val()+";||;";
          if($(this).find("input[name='true_or_not[]']").is(":checked")){
            $require += $(this).find("input[name='true_or_not[]']").val()+";||;";
          }else{
            $require +="0"+";||;";
          }

        }
        $opt_input ="";

        $(this).find("#optSquare .item-opt-row").each(function(j){
          if($(this).parents(".item-opt-div").find(".item-opt-row").length==j+1){

              $opt_input+=$(this).find("input[name='item_opt_input[]']").val();
          }else{

              $opt_input+=$(this).find("input[name='item_opt_input[]']").val()+";|";
          }

        });

        $examples = "<input type=\"hidden\" name=\"examples[]\" value=\""+$opt_input+"\">";
        $questions = "<input type=\"hidden\" name=\"questions[]\" value=\""+$question+"\">";
        $types = "<input type=\"hidden\" name=\"types[]\" value=\""+$type+"\">";
        $req_check = "<input type=\"hidden\" name=\"req_check[]\" value=\""+$require+"\">";

        if($(this).find("input[name='examples[]']").length > 0){
            $(this).find("input[name='examples[]']").val($opt_input);
          }else{
            $(this).append($examples);
        }

         if($(".item-container").find("input[name='questions[]']").length > 0){
            $(".item-container").find("input[name='questions[]']").val($question);
          }else{
            $(".item-container .js-to-server").append($questions);
        }

        if($(".item-container").find("input[name='types[]']").length > 0){
            $(".item-container").find("input[name='types[]']").val($type);
          }else{
            $(".item-container .js-to-server").append($types);
        }

         if($(".item-container").find("input[name='req_check[]']").length > 0){
            $(".item-container").find("input[name='req_check[]']").val($require);
          }else{
            $(".item-container .js-to-server").append($req_check);
        }
        
      });
      html2CanvasPicture();
    },

    this.optRemove = function(e){

      var optSquare = $(e).parents("#optSquare");
      var opt_row_num = optSquare.find(".item-opt-div .item-opt-row").length-2;

      if(opt_row_num<0){
          alert('더이상 삭제 할 수 없습니다.');
      }else{
        optSquare.find(".item-opt-div .item-opt-row").eq(opt_row_num).find(".input-style1").focus();
        $(e).parents(".item-opt-row").remove();
        //e.parentNode.parentNode.remove();
      }
    },

    this.optAdd = function(e, opt_type){
     var item_opt_type = $("input[name='opt_select[]']").val();
     var optSquare = $(e).parents("#optSquare");
     var opt_row_num = optSquare.find(".item-opt-div .item-opt-row input:not(.input-gita)").length+1;
     
     if(opt_type == 1){

       if(item_opt_type==1)
       {
        optSquare.find(".item-opt-div").append(
        "<div class=\"item-opt-row column\"><div class=\"field \">"+
        "<i class=\"icon Radio large\"></i><input name=\"item_opt_input[]\" class=\"input-style1 input-radio\"  placeholder=\"옵션"+opt_row_num+"\"><a href=\"#optRemove\" onclick=\"survey.optRemove(this)\">"+
        "<i class=\"icon large Remove black\"></i></a></div></div>"
        );
        }
        else if(item_opt_type==2)
        {
        optSquare.find(".item-opt-div").append(
        "<div class=\"item-opt-row column\"><div class=\"field \">"+
        "<i class=\"icon Square Outline large\"></i><input name=\"item_opt_input[]\" class=\"input-style1 input-checkbox\"  placeholder=\"옵션"+opt_row_num+"\"><a href=\"#optRemove\" onclick=\"survey.optRemove(this)\">"+
        "<i class=\"icon large Remove black\"></i></a></div></div>"
        );
        }
        else if(item_opt_type==3){
        optSquare.find(".item-opt-div").append(
        "<div class=\"item-opt-row column\"><div class=\"field \">"+
        "<i class=\"icon List large\"></i><input name=\"item_opt_input[]\" class=\"input-style1 input-select\"  placeholder=\"옵션"+opt_row_num+"\"><a href=\"#optRemove\" onclick=\"survey.optRemove(this)\">"+
        "<i class=\"icon large Remove black\"></i></a></div></div>"
        );
        }
        else if(item_opt_type==4){
        
          alert('주관식형 옵션은 추가할 수 없습니다.');

        }else if(item_opt_type==5){
         
          alert('주관식형 옵션은 추가할 수 없습니다.');
        }

     }else{
         //기타일경우
        if(item_opt_type==3||item_opt_type==4||item_opt_type==5){
          alert('기타를 추가할 수 없습니다.');
        }else{
          if(optSquare.find(".item-opt-div .item-opt-row input.input-gita").length>=1){
            alert('기타는 하나만');
          }else{
            optSquare.find(".item-opt-div").append(
            "<div class=\"item-opt-row column\"><div class=\"field \">"+
            "<i class=\"icon Write large\"></i><input name=\"item-opt-gita[]\" class=\"input-style1 input-gita\"  placeholder=\"기타는 하나만 추가가능\" readonly=\"readonly\"><a href=\"#optRemove\" onclick=\"survey.optRemove(this)\">"+
            "<i class=\"icon large Remove black\"></i></a></div></div>"
          );
          }
        }
     }
      $(".item-container .item-group").removeClass("active");
      optSquare.parents(".item-group").addClass("active");
      optSquare.find(".item-opt-div .item-opt-row").eq(optSquare.find(".item-opt-div .item-opt-row").length-1).find("input").focus();
    },

    this.itemRemove = function(e){
      var itemGroup = $(e).parents(".item-container");
      var item_row_num = itemGroup.find(".item-group").length-2;

      if(item_row_num<0){
        alert('더이상 삭제 할 수 없습니다.');

      }else{

        $(e).parents(".item-group").remove();
        itemGroup.find(".item-group").eq(item_row_num).find("input[name='subject[]']").focus();
      }

    },
    this.itemAdd = function(e){
        var itemCont =$(".item-container");
        var opt_html ="<div class=\"ui raised segment grid item-group ui-sortable-handle active\">"+
                      "<div class=\"doubling two column row\"><div class=\"column\"><div class=\"field\">"+
        "<input type=\"text\" name=\"subject[]\" placeholder=\"질문을 입력하세요\" ></div> </div><div class=\"column item-sub-div\"><div class=\"customDropdownSearchTextInput ui selection dropdown item-opt-select\">"+
        "<input type=\"hidden\" name=\"opt_select[]\">"+
        "<div class=\"default text\"><i class=\"icon Selected Radio\"></i> 질문종류</div><i class=\"dropdown icon\"></i><div class=\"menu\">"+
        "<div class=\"item item-type\" data-value=\"1\"><i class=\"icon Selected Radio\"></i>객관식</div><div class=\"item item-type\" data-value=\"2\"><i class=\"icon Check Square\"></i>체크박스</div>"+
        "<div class=\"item item-type\" data-value=\"3\"><i class=\"icon Toggle Down\"></i>드롭다운</div><div class=\"item item-type\" data-value=\"4\"><i class=\"icon Text Cursor\"></i>단답형</div>"+
        "<div class=\"item item-type\" data-value=\"5\"><i class=\"icon Align Left\"></i>장문형</div></div> </div>  </div></div>"+
         
        "<div id=\"optSquare\" class=\"doubling one column row\"><div class=\"item-opt-div column\"><div class=\"item-opt-row column\">"+
        "<div class=\"field\"><i class=\"icon Radio large\"></i><input type=\"text\" name=\"item_opt_input[]\" class=\"input-style1\"  placeholder=\"옵션1\"><a href=\"#optRemove\" onclick=\"survey.optRemove(this)\"><i class=\"icon large Remove black\"></i></a></div>"+
        "</div></div>"+
        "<div class=\"column\">"+
        "<div class=\"field\"><i class=\"icon Warning large\"></i> <p class=\"display-in-b\"><b onclick=\"survey.optAdd(this, 1);\">옵션추가</b>&nbsp;또는&nbsp; <a href=\"#gita\" onclick=\"survey.optAdd(this, 2);\">기타추가</a></p></div></div>"+
        "<div class=\"column right aligned\"><div class=\"ui clearing divider\"></div><div class=\"row\"><i class=\"icon Copy link large\" onclick=\"survey.itemCopy(this);\"></i><i class=\"icon Trash link large\" onclick=\"survey.itemRemove(this)\"></i>"+
        "<div class=\"ui toggle checkbox\"><input type=\"checkbox\" name=\"true_or_not[]\" value=\"1\"><label>필수체크</label>"+
        "</div></div></div></div>";
        itemCont.find(".item-group").removeClass("active");
        
        itemCont.append(opt_html);
        itemCont.find(".item-group.active").find("input[name='subject[]']").focus();
        itemCont.find(".item-group.active .dropdown").dropdown("set selected", "1");
    },

    this.itemCopy = function(e){

     var itemCont = $(e).parents(".item-container");
     var item_now_num = $(e).parents(".item-group").index();
     var item_copy_item = $(e).parents(".item-group").clone();

     itemCont.find(".item-group").eq(item_now_num).after(item_copy_item);
     $(".item-group").removeClass("active");
     itemCont.find(".item-group").eq(item_now_num+1).addClass("active");
     itemCont.find(".item-group.active").find(".dropdown").dropdown(); 
     itemCont.find(".item-group").eq(item_now_num+1).find("input[name='subject[]']").focus();
    },
    
    this.optChange = function(e,val){

      $(e).parents(".item-group").find("#optSquare .item-opt-row").remove();

      if(val==1){
        var opt_html ="<div class=\"item-opt-row column\"><div class=\"field \">"+
          "<i class=\"icon Radio large\"></i><input class=\"input-style1 input-radio\"  placeholder=\"옵션1\" ><a href=\"#optRemove\" onclick=\"survey.optRemove(this)\">"+
          "<i class=\"icon large Remove black\"></i></a></div></div>";
          $(e).parents(".item-group").find("#optSquare .item-opt-div").append(opt_html);
      }else if(val==2){
         var opt_html ="<div class=\"item-opt-row column\"><div class=\"field \">"+
          "<i class=\"icon Square Outline large\"></i><input class=\"input-style1 input-checkbox\"  placeholder=\"옵션1\" ><a href=\"#optRemove\" onclick=\"survey.optRemove(this)\">"+
          "<i class=\"icon large Remove black\"></i></a></div></div>";
          $(e).parents(".item-group").find("#optSquare .item-opt-div").append(opt_html);
          
      }else if(val==3){
        var opt_html ="<div class=\"item-opt-row column\"><div class=\"field \">"+
          "<i class=\"icon List large\"></i><input class=\"input-style1 input-select\"  placeholder=\"옵션1\" ><a href=\"#optRemove\" onclick=\"survey.optRemove(this)\">"+
          "<i class=\"icon large Remove black\"></i></a></div></div>";
          $(e).parents(".item-group").find("#optSquare .item-opt-div").append(opt_html);
      }else if(val==4){
         var opt_html ="<div class=\"item-opt-row column\"><div class=\"field \">"+
          "<i class=\"icon Text Cursor large\"></i><input class=\"input-style2 input-short\"  placeholder=\"단답형\" readonly=\"readonly\"><a href=\"#optRemove\" onclick=\"survey.optRemove(this)\">"+
          "<i class=\"icon large Remove black\"></i></a></div></div>";
          $(e).parents(".item-group").find("#optSquare .item-opt-div").append(opt_html);
      }else if(val==5){
        var opt_html ="<div class=\"item-opt-row column\"><div class=\"field \">"+
          "<i class=\"icon Align Left large\"></i><input class=\"input-style3 input-long\"  placeholder=\"장문형\" readonly=\"readonly\"><a href=\"#optRemove\" onclick=\"survey.optRemove(this)\">"+
          "<i class=\"icon large Remove black\"></i></a></div></div>";
          $(e).parents(".item-group").find("#optSquare .item-opt-div").append(opt_html);
      }
    }

}

$(function () {
    $('.item-opt-div').sortable({
         //connectWith: ".item-opt-div",
        placeholder: "ui-state-highlight",
         //opacity: 0.8,
         //revert: true,

        receive: function (event, ui) {
          
        }
    });
    $("body .item-container").sortable({
        //connectWith: ".item-opt-div",
        placeholder: "ui-state-highlight",
        //opacity: 0.8,
        //revert: true,


      activate: function( event, ui ) {

        var el = $(ui.item['0']);
        $(".item-container .item-group").find("input[name='subject']").blur();
        $(".item-container .item-group").removeClass("active");
        el.addClass("active");
        $("input[name='subject']").blur();
       
        
      },deactivate: function( event, ui ) {


      }
    });

});
function html2CanvasPicture(){
html2canvas([document.getElementById('writeForm')], {
    onrendered: function (canvas) {
    var imagedata = canvas.toDataURL('image/png');

		var imgdata = imagedata.replace(/^data:image\/(png|jpg);base64,/, "");

		//ajax call to save image inside folder
		$.ajax({
			url: 'save_image.php',
			data: {
			       imgdata:imgdata
				   },
			type: 'post',
			success: function (response) {   
			   //$('#image_id img').attr('src', response);
			}
		});
    }
});
}