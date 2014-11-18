P2_Q4_C2='Category sequential information order';
P2_Q4_C3='Category relevance score';
category_order_error = "Wrong “Category sequential information order”.  Please, assign a different number between 1 and n (n=number of selected categories) for each row in the column “"+P2_Q4_C2+"”.";
category_relevance_score = "Wrong “Category relevance score”. Please, assign a different number between 1 and n (n=number of selected categories) for each row in the column “"+P2_Q4_C3+"”.";

function mk_popup(id,message)
{
    $('.'+id).qtip({
        content: message,
        show: 'mouseover',
        hide: 'mouseout',
        style: { 
            name: 'light', // Inherit from preset style
            width: 400
        }                    
    });
}
    
$(document).ready(function() {
    //$("#fq").maphilight();
        
    $(".img_add").click(function(){
        var id = $(this).attr('id');
        id = id.substring(0,id.length-4);
        var name = $("#"+id).attr('phase');
        addCategory(id,name);   
    });
    $(".img_rem").click(function(){
        var id = $(this).attr('id');
        id = id.substring(0,id.length-4);
        removeCategory(id);   
    });
       
    $("#reset_quest").click(
        function()
        {
            $.post("formsub.php?reset=true");
            location.reload();
        }
        );
    
    resizeMap("imap1",707);
    resizeMap("imap",707);
});
  
/*
function checkCategories(avoidScoreCheck)
{
    if(!checkCategoriesOrder())
    {
        alert(category_order_error);
        return false;
    }   
    if(!avoidScoreCheck && !checkCategoriesScore())
    {
        alert(category_relevance_score);
        return false;
    }   
    return true;
}
            
function checkCategoriesScore()
{
    try{
        var num =$("table#categories tr").size();
                  
        var s = "";
        var nums = new Array();
        $(".category_row").each(
            function(index){
                nums.push(parseInt($(this).find(".relevance").first().val()));
            });

        nums.sort(function(a,b){
            return a-b;
        });
                                            
        for(var i=1; i<=nums.length; i++)
        {
            if(i!=nums[i-1]) { alert(i+"!="+nums[i-1]); return false; }
        //$(".category_row")[nums[i-1]-i].remove();
        }
                            
        //                            current=-1;
        //                            for(var i=0; i<nums.length; i++)
        //                            {
        //                                if(nums[i]<=current) return false;
        //                                current=nums[i];
        //                            }
        return true;
    }
    catch(es)
    {
        return false;
    }
}
            
function checkCategoriesOrder()
{
    try{
        //Reorder cat
        var $table = $('table#categories');
        var $rows = $(".category_row",$table);
        $rows.sort(function(a, b){
            var keyA = parseInt($('.order',a).first().val());
            var keyB = parseInt($('.order',b).first().val());
            return keyA - keyB;                                   
        });
        $.each($rows, function(index, row){
            $table.append(row);
        });
                            
                            
        var num =$("table#categories tr").size();
                  
        var s = "";
        var nums = new Array();
        $(".category_row").each(
            function(index){
                nums.push(parseInt($(this).find(".order").first().val()));
            });

        //        var n = "";
        //        for(var i=1; i<=nums.length; i++) n+=""+nums[i-1];  
        //        alert(n);
        nums.sort(function(a,b){
            return a - b;
        });
        //        n="";
        //        for(var i=1; i<=nums.length; i++) n+=""+nums[i-1];
        //                alert(n);

        for(var i=1; i<=nums.length; i++)
        {
            if(i!=nums[i-1]) {
                //alert(i+' - '+nums[i-1]);
                return false;
            }
        //$(".category_row")[nums[i-1]-i].remove();
        }
                             
        updateCategories();
        return true;
                            
                            
    }
    catch(es)
    {
        alert(es);
        return false;
    }
}
*/   
function sortYPos(a,b){  
    return $(a).position().top >  $(b).position().top;
}; 

function updateRows(event,ui)
{
        var $table = $('table#categories');
        var $rows = $("tr.category_row",$table).sort(sortYPos);
        $.each($rows, function(index, row){
            //$('.order',row).first().val(index+1);
            $('.order input',$(row)).first().val(index+1);
            $('.order span',$(row)).first().html(index+1);
        });
}
   
function addCategory(id, name)
{      
    try{
        if(id=='') return false;
        if($("table #row_"+id).size()>0) return false;
        // Code between here will only run when the a link is clicked and has a name of addRow
        //var tb_name = name.replace(/ /g,"_");
        var num =$("table#categories tr").size()-1;
        $("table#categories tr:last").after(
            '<tr class="category_row" id="row_'+id+'">'+
            '<td class="dragger"><img src="img/updown.png" height="24"></td>' +
            '<td class="centered order" ><input type="input" style="display: none" size="3" class="order" name="order_'+id+'" id="order_'+id+'" value="'+num+'"/><span>'+num+'</span></td>'+
            '<td><input name="catname_'+id+'" id="catname_'+id+'" type="hidden" hidden="true" value="'+name+'" />' + name + '</td>' +
            '<td class="centered"><span id="relevance_stars_'+id+'">...</span><input type="hidden" id="relevance_'+id+'" name="relevance_'+id+'" /></td>'+
            '<td class="centered" ><textarea class="ar" id="comments_'+id+'" name="fw_'+id+'"  rows="3" cols="30" ></textarea></td>' +
            '<td><img class="row_rem" height="16" src="img/delete.png" onClick="removeCategory(\''+id+'\')" /></td></tr>');
        $('.ar').css('overflow', 'hidden').autogrow();
        
        //mk_popup("dragger","Drag the rows to change their order"); 
       
        $("#relevance_stars_"+id).raty({
                path:  'js/jquery.raty/img/small/',
                target     : '#relevance_'+id,
                targetType : 'number',
                targetKeep : true            
            });
            
        //$("#imap area").each(function(i){if($(this).attr("title")==name) alert($(this).attr("title"));});
                
        var buttons = $("#imap [id='"+id+"']");
        if(buttons.size()>=1 )
        {                 
            var pos = $("#fq").offset();
                       
            var area_coord = buttons.first().attr("coords").split(",");
            var min_x = 9999;
            var max_x = 0;
            var min_y = 9999;
            var max_y = 0;
            for(i=0; i<area_coord.length; i++)
            {
                if(i%2==0)
                {
                    min_x = Math.min(min_x, area_coord[i]);
                    max_x = Math.max(max_x, area_coord[i]);
                }
                else
                {
                    min_y = Math.min(min_y, area_coord[i]);
                    max_y = Math.max(max_y, area_coord[i]);
                }
            }

            var width = max_x-min_x;

            var left = parseInt(min_x) + pos.left;
            var top = parseInt(min_y) + pos.top;  
           
            $('#lo').before("<img class='highlight' width='"+width+"px' corner='"+buttons.first().attr("coords")+"' src='./img/"+id+".png' id='img_"+id+"' style='position:absolute; left:"+left+"; top:"+top+";' />");
            $('#lo').css('position', 'relative');
            $('#lo').css('z-index', 1);
            $('#bu').css('position', 'relative');
            $('#bu').css('z-index', 2);
        }
        updateCategories();
        return true;    
    }
    catch(ex)
    {       
        alert(name+":\n"+ex);
    }
}
   
function removeCategory(id)
{   
    var $table = $('table#categories');
    var rows = $(".category_row",$table);
    var after=rows.index($("table [id='row_"+id+"']"));
    if(after<0) return;
    for(var i=after; i<rows.length; i++)
    {
          $(rows[i]).find(".order").first().val($(rows[i]).find(".order").first().val()-1);
    }    

    $("table [id='row_"+id+"']").remove(); 
    $("#img_"+id).remove();
    

    updateCategories();
}
            
function updateCategories()
{
    if($("table#categories tr").size()>=3){
        $("#emptyrow").css("height","0px"); 
    } else if($("table#categories tr").size()<=2)
    {
        $("#emptyrow").css("height","20px"); 
    }
    
    $(".category_row").each(
        function(index){
            $(this).removeClass(index%2==0?"odd":"eaven");
            $(this).addClass(index%2==1?"odd":"eaven");
        });               

    var pos = $("#fq").offset();
    $('#fram_image .highlight').each(
        function(){
            var pos = $("#fq").position();
                       
            var area_coord = $(this).attr("corner").split(",");
            var min_x = 9999;
            var max_x = 0;
            var min_y = 9999;
            var max_y = 0;
            for(i=0; i<area_coord.length; i++)
            {
                if(i%2==0)
                {
                    min_x = Math.min(min_x, area_coord[i]);
                    max_x = Math.max(max_x, area_coord[i]);
                }
                else
                {
                    min_y = Math.min(min_y, area_coord[i]);
                    max_y = Math.max(max_y, area_coord[i]);

                }
            }

            var width = max_x-min_x;

            var left = parseInt(min_x)+ pos.left;
            var top = parseInt(min_y) + pos.top;  
		
            $(this).css("left",""+left);
            $(this).css("top",""+top);
        }
        );
        updateRows();
}
 

function resizeMap(map_name, original_width)
{
    var prevratio=1;
    var ratio = $("[usemap='#"+map_name+"']").attr("width")/original_width;
    rt=prevratio;
    prevratio=ratio;
    ratio/=rt;

   
    $("#"+map_name+" area").each(function() {
        var pairs = $(this).attr("coords").split(', ');
        for(var i=0; i<pairs.length; i++) {
            var nums = pairs[i].split(',');
            for(var j=0; j<nums.length; j++) {
                //console.log(nums[j]);
                //console.log(parseFloat(nums[j]) /2);
                nums[j] = parseFloat(nums[j]) * ratio;
            }
            pairs[i] = nums.join(',');
        }
        $(this).attr("coords", pairs.join(', '));
    });
}
