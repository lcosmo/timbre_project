current_phase="Phase";
current_typ = "Typology";

var colhead = {};
colhead['Regulation']=       "<tr class='colhead'><td>Subcategory</td><td>Country of reference</td><td>Application scale</td><td>Title of the document (english)</td><td>Description of the document (english)</td><td>Original language link</td><td>English Version link</td><td>Other languages link</td><td>Rating</td></tr>";
colhead['Technical manuals']="<tr class='colhead'><td>Subcategory</td><td>Country of reference</td><td>Application scale</td><td>Title of the document (english)</td><td>Description of the technical document</td><td>Original language link</td><td>English Version link</td><td>Other languages link</td><td>Rating</td></tr>";
colhead['Tools']=            "<tr class='colhead'><td>Subcategory</td><td>Country of reference</td><td>Application scale</td><td>Name of the tool</td><td>Description of the Tool</td><td>Original language link</td><td>English Version link</td><td>Other languages link</td><td>Rating</td></tr>";
colhead['Case studies']=     "<tr class='colhead'><td>Subcategory</td><td>Country of reference</td><td>Application scale</td><td>Case study name</td><td>Description of the case study</td><td>Original language link</td><td>English Version link</td><td>Other languages link</td><td>Rating</td></tr>";

colhead['us_Regulation']=       "<tr class='colhead'><td>Link Status</td><td>Subcategory</td><td>Country of reference</td><td>Application scale</td><td>Title of the document (english)</td><td>Description of the document (english)</td><td>Original language link</td><td>English Version link</td><td>Other languages link</td><td>Rating</td></tr>";
colhead['us_Technical manuals']="<tr class='colhead'><td>Link Status</td><td>Subcategory</td><td>Country of reference</td><td>Application scale</td><td>Title of the document (english)</td><td>Description of the technical document</td><td>Original language link</td><td>English Version link</td><td>Other languages link</td><td>Rating</td></tr>";
colhead['us_Tools']=            "<tr class='colhead'><td>Link Status</td><td>Subcategory</td><td>Country of reference</td><td>Application scale</td><td>Name of the tool</td><td>Description of the Tool</td><td>Original language link</td><td>English Version link</td><td>Other languages link</td><td>Rating</td></tr>";
colhead['us_Case studies']=     "<tr class='colhead'><td>Link Status</td><td>Subcategory</td><td>Country of reference</td><td>Application scale</td><td>Case study name</td><td>Description of the case study</td><td>Original language link</td><td>English Version link</td><td>Other languages link</td><td>Rating</td></tr>";



callid=0;
function getSearchedItems(searchval, user)
{
    //alert(searchval);
    //alert(user);
    if(user===undefined)
        user='';
        
    if(searchval=='search into the database')
        searchval = '';
    
    callid++;
    document.getElementById("explore_content").innerHTML="Finding";
    //alert(current_phase);
    if(current_phase=="Phase" || current_typ=="Typology") 
    {
        $("#explore_content").html('<div id="framework"><img id="framimg" src="img/Framework_q.png"  usemap="#Image-Map"  /></div>');
        return;
    }
	
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    var array_of_checked_values = $("[name=multiselect_country_filter]:checked").map(
        function(){return $(this).attr("title");} ).get();
//    var array_of_checked_values = $("#country_filter").multiselect().map(function(){
//        alert(">"+this.value);
//        return this.value;	
//    }).get();
    
    var values = new Array();
    $.each($("#country_filter:checked"), function() {
    values.push($(this).val());
    // or you can do something to the actual checked checkboxes by working directly with  'this'
    // something like $(this).hide() (only something useful, probably) :P
    });

            
    //$("#explore_content").html("utils/data_retriever.php?id="+callid+"&user="+user+"&query="+searchval+"&phase="+current_phase+"&typ="+current_typ+"&country="+array_of_checked_values);
    //return;
    xmlhttp.open("GET","utils/data_retriever.php?id="+callid+"&user="+user+"&query="+searchval+"&phase="+current_phase+"&typ="+current_typ+"&country="+array_of_checked_values,true);
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            
            
          
            xmlDoc=xmlhttp.responseXML;		
            txt="<table id='itemlist'>";
            txt+="<tbody>";
		
                
                
            elcount = xmlDoc.getElementsByTagName("item").length;
            try
            {			
                $("#foundcount").html("Found "+elcount+" items");
            }
            catch(ex){} //No searching
            var id = xmlDoc.getElementsByTagName("root")[0].getAttribute("id");
            if(id!=callid) return;
                        

            if(elcount==0) {
                $("#explore_content").html("No results");
                return;
            }
            phases=xmlDoc.getElementsByTagName("phase");
            cur_phase="";
            vur_typ="";
            odd=true;
            
          //  $(".ui-multiselect-checkboxes label").css('color','#AAA');
            //            
//            
//            $("#country_filter").find('option').each(function (i,e)
//                {
//                    
//                  $('ui-multiselect').html('--');  
//                }
//        );
                    
                    
            for (pi=0;pi<phases.length;pi++)
            {
                phase_name = phases[pi].getAttribute("name");
                if(phase_name==null) continue;
                if(cur_phase!=phase_name && (current_phase=="All" || cur_phase==""))
                {
                    txt+="<tr class='phasename'><td colspan='9'>Cathegory: "+phase_name+"</td></tr>";			
                    cur_phase=phase_name;
                    cur_typ="";
			
                }			  
                var typs = phases[pi].getElementsByTagName("typology");
                for (ti=0;ti<typs.length;ti++)
                {
                    typ_name = typs[ti].getAttribute("name");
                    if(cur_typ!=typ_name)
                    {
                        txt+=colhead[typ_name];
                        cur_typ = typ_name;
                        odd=true;
                    }
                    items = typs[ti].getElementsByTagName("item");
                    for(ii=0; ii<items.length; ii++)
                    {
                        var id = getColVal(items[ii].getElementsByTagName("id")[0].childNodes[0]);
                        txt+="<tr class='"+(odd?"odd":"even")+"'>";
                        txt+="<td>"+getColVal(items[ii].getElementsByTagName("subcategory")[0].childNodes[0]) + "</td>";

                        var country = getColVal(items[ii].getElementsByTagName("country")[0].childNodes[0]);
                        txt+="<td>"+country + "</td>";
                                                
                        //$('[for=ui-multiselect-cof_opt_'+country+']').removeAttr('style');
                        
                        txt+="<td>"+getColVal(items[ii].getElementsByTagName("influence")[0].childNodes[0]) + "</td>";

                        txt+="<td>"+getColVal(items[ii].getElementsByTagName("title")[0].childNodes[0]) + "</td>";
				
                        txt+="<td>"+getColVal(items[ii].getElementsByTagName("description")[0].childNodes[0]) + "</td>";

                        var rated = !(getColVal(items[ii].getElementsByTagName("vnum")[0].childNodes[0])>0 && getColVal(items[ii].getElementsByTagName("rated")[0].childNodes[0])==0);
                        var first_click = getColVal(items[ii].getElementsByTagName("vnum")[0].childNodes[0])==0;
                        var click_event = first_click?"onclick='rate("+id+")'":"";
                                              
                        var orilang = items[ii].getElementsByTagName("link_ori")[0].getAttribute("lang");
                        var orilink = "goto.php?pid="+id+"&url="+escape(getColVal(items[ii].getElementsByTagName("link_ori")[0].childNodes[0]))
                        txt+="<td><a "+click_event+" target='blank' href='"+ orilink + "'>"+ (orilang!="goto.php?pid="+id+"&url="?orilang+" version":"") + "</a></td>";
				
                        var link_eng = "goto.php?pid="+id+"&url="+escape(getColVal(items[ii].getElementsByTagName("link_eng")[0].childNodes[0]));
                        txt+="<td><a "+click_event+" target='blank' href='"+ link_eng + "'>"+(link_eng!="goto.php?pid="+id+"&url="?"English version":"&nbsp;")+"</a></td>";
				
                        lang_other = getColVal(items[ii].getElementsByTagName("lang_other")[0].childNodes[0]);
                        lang_other= lang_other==""?"Other version":lang_other;
                        var link_other = "goto.php?pid="+id+"&url="+escape(getColVal(items[ii].getElementsByTagName("link_other")[0].childNodes[0]));
                        
                        txt+="<td><a "+click_event+" target='blank' href='"+ link_other + "'>"+(link_other!="goto.php?pid="+id+"&url="?lang_other+" version":"&nbsp;")+"</a></td>";
                        
                        var neval = getColVal(items[ii].getElementsByTagName("nrates")[0].childNodes[0]);
                        var ninap = getColVal(items[ii].getElementsByTagName("inap")[0].childNodes[0]);
                        var totvis = getColVal(items[ii].getElementsByTagName("nvisits")[0].childNodes[0]);
                        
                        var inapstyle=ninap>0?"color:red":"";
                        
                        var rate = Math.round(getColVal(items[ii].getElementsByTagName("rating")[0].childNodes[0]));
                        txt+="<td  style='text-align: center;white-space: nowrap'><span id='rating"+id+"' class='rating' value='"+rate+"'></span>"+
                             "<br><p style='font-size: 50%;text-align:left'>Evaluations: "+neval+"<br><span style='"+inapstyle+"'>Inappropriate: "+ninap+"</span><br>Total visits: "+totvis+"</p></td>";

                        if(getColVal(items[ii].getElementsByTagName("moreinfo")[0].childNodes[0])=='yes')
                            txt+="<td><input type='button' value='More Info' onclick='showMoreInfo("+ id + ")' /></td>";
 
                
                        if(getColVal(items[ii].getElementsByTagName("canmodify")[0].childNodes[0])=='true')
                            {
                                txt+="<td><img src='img/modify.png' onclick='modify_item(\""+id+"\")' alt='Modify' title='Modify' /></td>";
                                txt+="<td><img src='img/delete.png' onclick='delete_item(\""+id+"\")' alt='Delete' title='Delete' /></td>";    
                            }
                            else
                            {
                                txt+="<td><img src='img/modify_d.png' onclick='superuserpopup()' alt='modify' title='Modify' /></td>";
                                txt+="<td><img src='img/delete_d.png' onclick='superuserpopup()' alt='delete' title='Delete' /></td>";    
                            } 
                               
                        txt+="</tr>";
                        
                        
                        //SCRIPT
                        if(!rated)
                            txt+="<script>$(\"#rating"+id+"\").click(function (){rate("+id+");});   animaterating("+id+");</script>";
                        else
                            txt+="<script>$(\"#rating"+id+"\").click(function (){rate("+id+");});</script>";
                        
                        txt+=
                        "<script>$(\"#rating"+id+"\").raty({"+
                            "path:  'js/jquery.raty/img/small/',"+
                            "width: '80px',space: false,"+
                            "readOnly: true," +
                            "score: "+rate+
                        "});</script>";   
                
                    }
                    odd=!odd;
                }
            }
            $("#explore_content").html(txt+"</tbody></table>");
        }
    }
    xmlhttp.send();
}

function getColVal(item)
{
    if (item!=null)	return item.nodeValue;
    return "";
}

function getExploreItems()
{
    //aler("exp")
    getSearchedItems("",111);
}

function getUserItems(searchval, user)
{
    //user=310;
    //alert(searchval);
    //alert(user);
    if(user===undefined)
        user='';
        
    if(searchval=='search into the database')
        searchval = '';
    
    callid++;
    document.getElementById("explore_content").innerHTML="Finding";
    //alert(current_phase);
    if(current_phase=="Phase" || current_typ=="Typology") 
    {
        $("#explore_content").html('<div id="framework"><img id="framimg" src="img/Framework_q.png"  usemap="#Image-Map"  /></div>');
        return;
    }
	
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    var array_of_checked_values = $("[name=multiselect_country_filter]:checked").map(
        function(){return $(this).attr("title");} ).get();
//    var array_of_checked_values = $("#country_filter").multiselect().map(function(){
//        alert(">"+this.value);
//        return this.value;	
//    }).get();
    
    var values = new Array();
    $.each($("#country_filter:checked"), function() {
    values.push($(this).val());
    // or you can do something to the actual checked checkboxes by working directly with  'this'
    // something like $(this).hide() (only something useful, probably) :P
    });

    //$("#explore_content").html("utils/data_retriever.php?id="+callid+"&user="+user+"&query="+searchval+"&phase="+current_phase+"&typ="+current_typ+"&country="+array_of_checked_values);
    //return;
    xmlhttp.open("GET","utils/data_retriever.php?id="+callid+"&user="+user+"&query="+searchval+"&phase="+current_phase+"&typ="+current_typ+"&country="+array_of_checked_values,true);
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            xmlDoc=xmlhttp.responseXML;		
            txt="<table id='itemlist'>";
            txt+="<tbody>";
		
            elcount = xmlDoc.getElementsByTagName("item").length;
            try
            {			
                $("#foundcount").html("Found "+elcount+" items");
            }
            catch(ex){} //No searching
            var id = xmlDoc.getElementsByTagName("root")[0].getAttribute("id");
            if(id!=callid) return;
                        

            if(elcount==0) {
                $("#explore_content").html("No results");
                return;
            }
            phases=xmlDoc.getElementsByTagName("phase");
            cur_phase="";
            vur_typ="";
            odd=true;
            for (pi=0;pi<phases.length;pi++)
            {
                phase_name = phases[pi].getAttribute("name");
                if(phase_name==null) continue;
                if(cur_phase!=phase_name && (current_phase=="All" || cur_phase==""))
                {
                    txt+="<tr class='phasename'><td colspan='9'>Cathegory: "+phase_name+"</td></tr>";			
                    cur_phase=phase_name;
                    cur_typ="";
			
                }			  
                var typs = phases[pi].getElementsByTagName("typology");
                for (ti=0;ti<typs.length;ti++)
                {
                    typ_name = typs[ti].getAttribute("name");
                    if(cur_typ!=typ_name)
                    {
                        txt+=colhead['us_'+typ_name];
                        cur_typ = typ_name;
                        odd=true;
                    }
                    items = typs[ti].getElementsByTagName("item");
                    for(ii=0; ii<items.length; ii++)
                    {
                        var id = getColVal(items[ii].getElementsByTagName("id")[0].childNodes[0]);
                        txt+="<tr class='"+(odd?"odd":"even")+"'>";
                        //status
                        var health = getColVal(items[ii].getElementsByTagName("health")[0].childNodes[0]);
                        var health_img = 'green.png';
                        if(health<=0.2) health_img='red.png';
                        if(health>0.2 && health<1) health_img='yellow.png';
                        
                        txt+="<td style='display: block; margin: 0 auto; text-align: center;' ><img src='img/status/"+health_img+"' title='helth: "+health+"'/></td>";
    
                        txt+="<td>"+getColVal(items[ii].getElementsByTagName("subcategory")[0].childNodes[0]) + "</td>";

                        txt+="<td>"+getColVal(items[ii].getElementsByTagName("country")[0].childNodes[0]) + "</td>";
                        txt+="<td>"+getColVal(items[ii].getElementsByTagName("influence")[0].childNodes[0]) + "</td>";

                        txt+="<td>"+getColVal(items[ii].getElementsByTagName("title")[0].childNodes[0]) + "</td>";
				
                        txt+="<td>"+getColVal(items[ii].getElementsByTagName("description")[0].childNodes[0]) + "</td>";

                        var rated = !(getColVal(items[ii].getElementsByTagName("vnum")[0].childNodes[0])>0 && getColVal(items[ii].getElementsByTagName("rated")[0].childNodes[0])==0);
                        var first_click = getColVal(items[ii].getElementsByTagName("vnum")[0].childNodes[0])==0;
                        var click_event = first_click?"onclick='rate("+id+")'":"";
                                              
                        var orilang = items[ii].getElementsByTagName("link_ori")[0].getAttribute("lang");
                        var orilink = "goto.php?pid="+id+"&url="+escape(getColVal(items[ii].getElementsByTagName("link_ori")[0].childNodes[0]))
                        txt+="<td><a "+click_event+" target='blank' href='"+ orilink + "'>"+ (orilang!="goto.php?pid="+id+"&url="?orilang+" version":"") + "</a></td>";
				
                        var link_eng = "goto.php?pid="+id+"&url="+escape(getColVal(items[ii].getElementsByTagName("link_eng")[0].childNodes[0]));
                        txt+="<td><a "+click_event+" target='blank' href='"+ link_eng + "'>"+(link_eng!="goto.php?pid="+id+"&url="?"English version":"&nbsp;")+"</a></td>";
				
                        lang_other = getColVal(items[ii].getElementsByTagName("lang_other")[0].childNodes[0]);
                        lang_other= lang_other==""?"Other version":lang_other;
                        var link_other = "goto.php?pid="+id+"&url="+escape(getColVal(items[ii].getElementsByTagName("link_other")[0].childNodes[0]));
                        
                        txt+="<td><a "+click_event+" target='blank' href='"+ link_other + "'>"+(link_other!="goto.php?pid="+id+"&url="?lang_other+" version":"&nbsp;")+"</a></td>";
                        
                        var neval = getColVal(items[ii].getElementsByTagName("nrates")[0].childNodes[0]);
                        var ninap = getColVal(items[ii].getElementsByTagName("inap")[0].childNodes[0]);
                        var totvis = getColVal(items[ii].getElementsByTagName("nvisits")[0].childNodes[0]);
                        
                        var inapstyle=ninap>0?"color:red":"";
                        
                        var rate = Math.round(getColVal(items[ii].getElementsByTagName("rating")[0].childNodes[0]));
                        txt+="<td  style='text-align: center;white-space: nowrap'><span id='rating"+id+"' class='rating' value='"+rate+"'></span>"+
                             "<br><p style='font-size: 50%;text-align:left'>Evaluations: "+neval+"<br><span style='"+inapstyle+"'>Inappropriate: "+ninap+"</span><br>Total visits: "+totvis+"</p></td>";

                        if(getColVal(items[ii].getElementsByTagName("moreinfo")[0].childNodes[0])=='yes')
                            txt+="<td><input type='button' value='More Info' onclick='showMoreInfo("+ id + ")' /></td>";
 
                
                        if(getColVal(items[ii].getElementsByTagName("canmodify")[0].childNodes[0])=='true')
                            {
                                txt+="<td><img src='img/modify.png' onclick='modify_item(\""+id+"\")' alt='Modify' title='Modify' /></td>";
                                txt+="<td><img src='img/delete.png' onclick='delete_item(\""+id+"\")' alt='Delete' title='Delete' /></td>";    
                            }
                            else
                            {
                                txt+="<td><img src='img/modify_d.png' onclick='superuserpopup()' alt='modify' title='Modify' /></td>";
                                txt+="<td><img src='img/delete_d.png' onclick='superuserpopup()' alt='delete' title='Delete' /></td>";    
                            } 
                               
                        txt+="</tr>";
                        
                        
                        //SCRIPT
                        if(!rated)
                            txt+="<script>$(\"#rating"+id+"\").click(function (){rate("+id+");});   animaterating("+id+");</script>";
                        else
                            txt+="<script>$(\"#rating"+id+"\").click(function (){rate("+id+");});</script>";
                        
                        txt+=
                        "<script>$(\"#rating"+id+"\").raty({"+
                            "path:  'js/jquery.raty/img/small/',"+
                            "width: '80px',space: false,"+
                            "readOnly: true," +
                            "score: "+rate+
                        "});</script>";   
                
                    }
                    odd=!odd;
                }
            }
            $("#explore_content").html(txt+"</tbody></table>");
        }
    }
    xmlhttp.send();
}

function animaterating(id, blink)
{
    blink = typeof blink !== 'undefined' ? blink : true;
    $("#rating"+id).attr("blinking",""+blink);
    //$("#rating"+id).delay(600).clearQueue().queue(function(next){animaterating1(id);});    
}

function rate(id)
{
    animaterating(id);
    showRating(id);
    
    $("#rating"+id).raty({
        path:  'js/jquery.raty/img/small/',
        width: '80px',space: false,
        readOnly: true,
        score: $("#rating"+id).attr('value')
    });
}


function showRating(id)
{
    //alert("ok"+id);
    $("#rating_container").fadeOut(100);
    $.ajax({
        url: "rating.php?id="+id
    }).done(function ( data ) {
        $("#rating_div").html(data);
        $("#rating_container").fadeIn(200);
    });            
}