/*
<?php
        $phases[0] = 'Strategic planning';
        $phases[1] = 'Investigation (preliminary/detailed)';
        $phases[2] = 'Risk assessment (qualitative/quantitative)';
        $phases[3] = 'Remediation strategies and options';
        $phases[4] = 'Remediation technologies evaluation and selection';
        $phases[5] = 'Building and infrastructure documents';
        $phases[6] = 'Deconstruction/re-use of structures materials';
        $phases[7] = 'Waste management';
        $phases[8] = 'Requalification plan development';
        $phases[9] = 'Implementation, control, monitoring (land back to market)';
        $phases[10] = 'Socio-economic assessment';
        $phases[11] = 'Funding and financing';
        $phases[12] = 'Decision-making and communication';
                
        $subcategories[$phases[1]] = array('Preliminary', 'Detailed', 'Preliminary and Detailed');
        $subcategories[$phases[2]] = array('Qualitative', 'Quantitative', 'Qualitative and Quantitative');
?>
 */

current_phase="All";
current_typ="Regulation";

function show_insert(show)
{
    $('#insert').unbind('click');
    $('#insertform')[0].reset();    
    update_selectors(false);
    $('#insert').val("Insert");
    $('#insert').attr("name",'insertitem');
    $('#insert').click(function ()
    {
        $("#insertform").submit();
//        $.post("modify.php?insertitem=true&", $('#insertform').serialize(), 
//        function (){
//            show_insert();
//            update_selectors(true);
//        })
    }   
    );
            
    if(show)
    {
		
        $('#insert_div').show();
        $('#searchbar').hide();
    }
    else
    {	
        $('#insert_div').hide();
        $('#searchbar').show();
    }
}

function delete_item(id)
{
    if(confirm("Delete item?"))
    {
        $.post("utils/data_retriever.php", "delete=true&id="+id, function(data){
                    if(startsWith(""+data,"ERROR"))
                        alert(data);
                    else
                        {
                            getExploreItems();
                        }
                    } );               
    }
}

function startsWith(haystack, needle)
{
    return haystack.indexOf(needle) == 0;
}

function update_selectors(norefresh)
{
    //sidebar and tabs
    $(".phase").removeClass("selected");
    $(".phase:contains('"+current_phase+"')").addClass("selected");
    $(".typ").removeClass("selected");
    $(".typ:contains('"+current_typ+"')").addClass("selected");
	
    //search_select
    $("#phase_select").val(current_phase).attr("selected", "selected");
    $("#typ_select").val(current_typ).attr("selected", "selected");
	
    //insert_select
    $("#insert_phase_select").val(current_phase).attr("selected", "selected");
    $("#insert_typ_select").val(current_typ).attr("selected", "selected");
    //subcategory            
    $('option', $('#subcategory')).remove();
    if(false) {}
        <?          
        foreach($subcategories as $phase=>$subcats)
        {
            echo "else if(current_phase==\"".$phase."\"){\n";
            foreach($subcats as $option)
//            echo "$('#subcategory').append( new Option('".$option."'));\n";
            echo "$('#subcategory').append('<option>".$option."</option>');\n";
            echo "}";
        }
        ?>
        if($('#subcategory option').length==0)
            $('#subcategory').append("<option>None</option>");
            
    //insert addition info
            
    if(current_phase=="<?= $phases[3] ?>" && current_typ=="Tools")
    {
        $(".additionalinfo").show();
        $('#moreinfo').val("yes");
    }else{
        $(".additionalinfo").hide();
        $('#moreinfo').val("no");
    }
        
    //navigator
    if(current_phase!="Category" && current_typ!="Typology")
        $("#position").html("<a href=\"explore.php\">Timbre</a> > "+(searchMode?"search":"explore")+" > "+current_phase+" - "+current_typ);
    else
        $("#position").html("<a href=\"explore.php\">Timbre</a> > "+(searchMode?"search":"explore")+" ");
	
    $("#framework_link a").removeClass("selected");
    if(current_phase=="Category" || current_typ=="Typology")
    {
        $("#framework_link a").addClass("selected");
    }
	
    if(!norefresh)
        {
//        if(searchMode)
            getSearchedItems($("#query").val());
//        else                
//            getExploreItems();
        }
}

function showMoreInfo(id)
{
    $("#moreinfo_container").fadeOut();
    $.ajax({
        url: "moreinfo.php?id="+id
    }).done(function ( data ) {
        $("#moreinfo_div").html(data);
        $("#moreinfo_container").fadeIn();

        var scrollTop = $(window).scrollTop();
        $("#moreinfo_container").offset({top : Math.max(200,scrollTop+($(window).height() - $("#moreinfo_container").height())/2) });

    });         
}



prevratio=1;
function resizeMap()
{    
    var ratio = $("#framimg").width()/720;
    rt=prevratio;
    prevratio=ratio;
    ratio/=rt;
	
    $("area").each(function() {	    
		
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

searchMode=false;
function setSearchMode(val)
{
//    if(val)
//    {
//        if(searchMode) return;
//        current_phase="All";
//        current_typ = "All";
//        $(".search_all").show();
//        
//        $("#insert_block").hide(); 
//        $("#search_results_block").show();    
//    }
//    else
//    {
//        current_phase="Category";
//        current_typ = "Typology"; 
//        $(".search_all").hide();
//        
//        $("#insert_block").show();
//        $("#search_results_block").hide();
//    }
//    
//    searchMode=val;
//    update_selectors();
}


function modify_item(id)
{
    resid=id;
       $.ajax({
        type: "GET",
        url: "modify.php?id="+id,
        dataType: "xml",
        success: function(xml) {  
            
            show_insert(true);
            $('#insert').unbind('click');
            $('#insert').val("Save");
            $('#insert').attr("name",'modifyitem');
            $('#insert').click(function ()
                {
                    $.ajax({
                            type: "POST",
                            dataType: 'text',
                            url: "modify.php?modifyitem=true&id="+resid,
                            data: $('#insertform').serialize(),
                            success: function (){
//                                        alert('ok');
                //                      var myWindow = window.open("void.html","MsgWindow","width=200,height=100");
                //                      $(myWindow.document.body).html(asd);
                                        //$("#debug_w").html("asd");                        
                                        show_insert(false);
                                        update_selectors();
                                    },
                            error:  function(err){
//                                        alert(err);
                                    }
                           });
                });            
            $(xml).find('field').each(function(){
                var name = $(this).attr('name');
                //WARNING: fix
                if(name=="lang_ori") name="language";
                if(name=="phase") name="insert_phase_select";
                if(name=="typology") name="insert_typ_select";
                //
                
                var el = $("[name='"+name+"']");
                
                if(el.is("[type=checkbox]"))
                {          
                    
                    if($(this).text()!=0)
                        el.attr('checked', true);
                    else
                        el.removeAttr("checked");
                }
                else if(el.is("[type=radio]"))
                {
                    el.removeAttr("checked");
                    el.filter("[value='"+$(this).text()+"']").attr("checked","true");
                }
                else
                {                   
                    {
                        el.val($(this).text());
                    }
                }	
                el.change();
            // radiobutton
            });
        }
    });
}


function blinkIn()
{
    $(".rating[blinking=true]").each(function(){
        $(this).raty({
            path:  'js/jquery.raty/img/torate/',
            width: '80px',space: false,
            readOnly: true,
            score: $(this).attr('value')
        });
    });
    setTimeout(blibkOut,700);
}
function blibkOut()
{
    $(".rating").each(function(){
        $(this).raty({
            path:  'js/jquery.raty/img/small/',
            width: '80px',space: false,
            readOnly: true,
            score: $(this).attr('value')
        });
    });
    setTimeout(blinkIn,700);
}

function mk_popup(id,message)
{

    $('#'+id).qtip({
        content: message,
        show: 'mouseover',
        hide: 'mouseout',
        position: {
            corner: {
                tooltip: 'rightTop',
                target: 'leftBottom'
            }
        },
        style: { 
            name: 'light', // Inherit from preset style
            width: 400
        }                    
    });
}