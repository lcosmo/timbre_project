<form method="POST" id="insertform" >
    <table style="width:100%;white-space: nowrap;">
        <tr>
            <td style="width:20%" >Select the category of information to be updated: </td>
            <td><select id="insert_phase_select" name="insert_phase_select">
                    <?
                    foreach ($phases as $p)
                        echo "<option>" . $p . "</option>";
                    ?>
                </select>
                <select id="insert_typ_select" name="insert_typ_select">
                    <option>Typology</option>
                    <option>Regulation</option>
                    <option>Technical manuals</option>
                    <option>Tools</option>
                    <option>Case studies</option>
                </select>
            </td>
        </tr>
        <tr>
            <td >Sub-category:</td><td>
                <select name="subcategory" id="subcategory" class="selectinsertinput" >
                </select>        
            </td>
        </tr><tr>
            <td>Country of reference:</td>
            <td><select name="country" class="selectinsertinput" >
                    <?php
                    $langs = explode(",", file_get_contents("utils/countries.txt"));
                    foreach ($langs as $l)
                        echo "<option>" . $l . "</option>";
                    ?>
                </select> 
            </td>
        </tr><tr>
            <td>Application scale:</td>
            <td><select name="influence" class="selectinsertinput" >
                    <option>European</option>
                    <option>National</option>
                    <option>Regional</option>
                    <option>Local</option>
                    <option>Not applicable</option>
                </select> 
            </td>            
        </tr><tr>
            <td>Title in English: </td><td><input class="textinsertinput" type="text" name="title" /> </td>
        </tr><tr>
            <td>Description with key words in English:</td><td><textarea class="textinsertinput" name="description"></textarea></td>
        <tr>
            <td style="width:1%;">Original version language:</td>
            <td><select name="language" class="selectinsertinput" >
                    <?php
                    $langs = explode(",", file_get_contents("utils/languages.txt"));
                    foreach ($langs as $l)
                        echo "<option>" . $l . "</option>";
                    ?>
                </select>
            </td>
        </tr><tr>
            <td>Original version link:</td>
            <td><input class="textinsertinput" type="text" name="link_ori" /></td>
        </tr><tr>
            <td>English version link:</td>
            <td><input class="textinsertinput" type="text" name="link_eng" /></td>
        </tr><tr>
            <td>Other version link:</td>
            <td><input class="textinsertinput" type="text" name="link_other" />
                <select name="lang_other" class="selectinsertinput" >
                    <?php
                    $langs = explode(",", file_get_contents("utils/languages.txt"));
                    foreach ($langs as $l)
                        echo "<option>" . $l . "</option>";
                    ?>
                </select>
            </td></td>
            
        </tr><tr class="additionalinfo"><td><br/><h3>Additional Information</h3><input type="hidden" id="moreinfo" name="moreinfo" value="yes" /></td>
        </tr><tr class="additionalinfo">
            <td>Technology name:</td>
            <td><input class="textinsertinput" type="text" name="technoloyname" /></td>
        </tr><tr class="additionalinfo">
            <td>Technology type:</td>
            <td>
                <input type="checkbox" name="technologytype_1" value="In situ">In situ</input>
                <input type="checkbox" name="technologytype_2" value="Ex situ">Ex situ</input>
                <input type="checkbox" name="technologytype_3" value="Ex situ – On site">Ex situ – On site</input>
                <input type="checkbox" name="technologytype_4" value="Ex situ – Off site">Ex situ – Off site</input>
            </td>
        </tr><tr class="additionalinfo">
            <td>Environmental medium</td>
            <td>    
                <input type="checkbox" name="environmentalmedium_1" value="Soil">Soil</input>
                <input type="checkbox" name="environmentalmedium_2" value="Sediments">Sediments</input>
                <input type="checkbox" name="environmentalmedium_3" value="Groundwater">Groundwater</input>
            </td>
        </tr>

        <tr class="additionalinfo">
            <td><br/><STRONG>Target contaminants and performance (%)</strong></td>
        </tr>
        <tr class="additionalinfo">
            <td>NHVOC Nonhalogenated volatile organic compounds:</td>
            <td><input class="textinsertinput" type="text" name="nonhalogenatedvolatile" /></td>
        </tr><tr class="additionalinfo">
            <td>HVOC Halogenated volatile organic compouds:</td>
            <td><input class="textinsertinput" type="text" name="halogenatedvolatile" /></td>
        </tr>
        <tr class="additionalinfo">
            <td>NHSVOC Nonhalogenated semivolatile organic compounds:</td>
            <td><input class="textinsertinput" type="text" name="nonhalogenatedsemivolatile" /></td>
        </tr><tr class="additionalinfo">
            <td>HSVOC Halogenated semivolatile organic compounds :</td>
            <td><input class="textinsertinput" type="text" name="halogenatedsemivolatile" /></td>
        </tr><tr class="additionalinfo">
            <td>Inorganics (e.g. cyanide, sulfur, asbestos):</td>
            <td><input class="textinsertinput" type="text" name="inorganics" /></td>
        </tr><tr class="additionalinfo">
            <td>Metals / metalloids (e.g. Copper 25%, Iron 40%, etc.):</td>
            <td><input class="textinsertinput" type="text" name="metals" /></td>
        </tr><tr class="additionalinfo">
            <td>Fuels:</td>
            <td><input class="textinsertinput" type="text" name="fuels" /></td>
        </tr>
        </tr><tr class="additionalinfo">
            <td>Radionuclides:</td>
            <td><input class="textinsertinput" type="text" name="radionuclides" /></td>
        </tr>
        </tr><tr class="additionalinfo">
            <td>Explosives:</td>
            <td><input class="textinsertinput" type="text" name="explosives" /></td>
        </tr>
        <tr class="additionalinfo">
            <td><br/><STRONG>Technology applicability conditions</strong></td>
        </tr>
        <tr class="additionalinfo">
            <td>Annual average temperature (°C):</td>
            <td><input class="textinsertinput" type="text" name="annualtemperature" /></td>
        </tr><tr class="additionalinfo">
            <td>Remediation technology time scale:</td>
            <td>
                    <input class="textinsertinput" type="text" name="remediationtechnologytime" id="remediationtechnologytime" />
                    <select name="remediationtechnologytime_unit" id="remediationtechnologytime_unit">
                    <option>Weeks</option>
                    <option>Months</option>
                    <option>Years</option>
                    <option>m3/d</option>
                    <option>t/h</option>
                </select></td>
        </tr><tr class="additionalinfo">
            <td>Max achievable soil depth (m):</td>
            <td><input class="textinsertinput" type="text" name="maxsoil" /></td>
        </tr><tr class="additionalinfo">
            <td>Nature of soil:</td>
            <td>
                <input type="checkbox" name="soilnature_1" value="Gravel">Gravel</input>
                <input type="checkbox" name="soilnature_2" value="Sand">Sand</input>
                <input type="checkbox" name="soilnature_3" value="Silt">Silt</input>
                <input type="checkbox" name="soilnature_4" value="Clay">Clay</input>
            </td>
        </tr><tr class="additionalinfo">
            <td>Range of suitable organic carbon (e.g. 10 – 30 %; < 30%; > 10 %):</td>
            <td><input class="textinsertinput" type="text" name="organiccarbon" /></td>
        </tr>
        <tr class="additionalinfo">
            <td>Costs:</td>
            <td>
                <input class="textinsertinput" type="text" name="costs" id="costs" />
                <select name="costs_unit" id="costs_unit">
                    <option>€/m3</option>
                    <option>€/t</option>
                    <option>€/m2</option>
                </select></td>
        </tr>
<!--RATING-->   
        <tr><td colspan="2"><br/><h4>Evaluation of the provided information</h4></td></tr>
        <tr><td><strong>Clarity</strong><br/>
		<small>
		Is the information in the e-link clear in the description of concepts <br/>
                and in the use of the specific vocabulary?
		</small>
		</td><td><span class="stars" style="position:relative; top:5px;" id="star1" name="1" style=""></span><input type='hidden' name='rating1' id='rating1'  onchange="$('#star1').raty('score', $(this).val());" /></td></tr>
        
		<tr><td><strong>Reliability and accuracy</strong><br/>
		<small>
		Are the source of information or the authors “officially” recognised <br/>
		( e.g.,  well known scientists, public authorities) and the information <br/>
		accurate and trustworthy?
		</small>
		
		</td><td><span class="stars" style="position:relative; top:5px;" id="star2" name="2" style=""></span><input type='hidden' name='rating2' id='rating2'  onchange="$('#star2').raty('score', $(this).val());" /></td></tr>
        	
		<tr><td><strong>Updating</strong> <br/>
		<small>
		Is the information in the e-link up to date and in line with the latest <br/>
                regulatory prescriptions?
		</small>		
		</td><td><span class="stars" style="position:relative; top:5px;" id="star3" name="3" style=""></span><input type='hidden' name='rating3' id='rating3'  onchange="$('#star3').raty('score', $(this).val());" /></td></tr>
</table>
    <div id="submint_buttons">
        <input type="button" value="Cancel" id="cancel" onClick="show_insert(false)" />
        <input type="button" value="Insert" name="insertitem" id="insert" />
    </div>
</form>


<script>
$('.stars').each(function( index ) {
    $(this).raty({
        path:  'js/jquery.raty/img/',
        width: '150px',
        target     : '#rating'+$(this).attr('name'),
        targetType : 'number',
        targetKeep : true
    });
    //$('#rating'+$(this).attr('name')).rules('add',{ required: true});
});
</script>   