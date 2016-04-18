
var monthtext=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sept','Oct','Nov','Dec'];

function populatedropdown(dayfield, monthfield, yearfield){
    var today=new Date()
    var dayfield=document.getElementById(dayfield)
    var monthfield=document.getElementById(monthfield)
    var yearfield=document.getElementById(yearfield)
    
	for (var i=0; i<31; i++)
        dayfield.options[i]=new Option(i+1, i+1)
        dayfield.options[today.getDate()-1].selected = true //select today's day
    
	for (var m=0; m<12; m++)
        monthfield.options[m]=new Option(monthtext[m], m+1)
        monthfield.options[today.getMonth()]=new Option(monthtext[today.getMonth()], today.getMonth()+1, true, true) //select today's month
		var thisyear=today.getFullYear()
    
	for (var y=0; y<20; y++){
        yearfield.options[y]=new Option(thisyear, thisyear)
        thisyear-=1
    }
    yearfield.options[0]=new Option(today.getFullYear(), today.getFullYear(), true, true) //select today's year
}

function setdropdown(dayfield, day) {
    var dayfield=document.getElementById(dayfield);
    dayfield.options[day-1].selected = true;
}

