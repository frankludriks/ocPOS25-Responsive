var myimages=new Array()

function preloadimages(){
	for (i=0;i<preloadimages.arguments.length;i++){
		myimages[i]=new Image();
		myimages[i].src=preloadimages.arguments[i];
	}
}


preloadimages("images/PrevYrOff40x40.jpg","images/PrevYrOn40x40.jpg","images/PrevMoOff40x40.jpg","images/PrevMoOn40x40.jpg","images/NextYrOff40x40.jpg","images/NextYrOn40x40.jpg","images/NextMoOff40x40.jpg","images/NextMoOn40x40.jpg");

var thisDate = 1;							
var wordMonth = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
var today = new Date();							
var todaysDay = today.getDay() + 1;					
var todaysDate = today.getDate();					
var todaysMonth = today.getUTCMonth() + 1;				
var todaysYear = today.getFullYear();					
var monthNum = todaysMonth;						
var yearNum = todaysYear;						
var firstDate = new Date(String(monthNum)+"/1/"+String(yearNum));
var firstDay = firstDate.getUTCDay();					
var lastDate = new Date(String(monthNum+1)+"/0/"+String(yearNum));
var numbDays = 0;
var calendarString = "";
var eastermonth = 0;
var easterday = 0;


function changedate(buttonpressed) {
	if (buttonpressed == "prevyr") yearNum--;
	else if (buttonpressed == "nextyr") yearNum++;
	else if (buttonpressed == "prevmo") monthNum--;
	else if (buttonpressed == "nextmo") monthNum++;
	else  if (buttonpressed == "return") { 
		monthNum = todaysMonth;
		yearNum = todaysYear;
	}

	if (monthNum == 0) {
		monthNum = 12;
		yearNum--;
	}
	else if (monthNum == 13) {
		monthNum = 1;
		yearNum++
	}

	lastDate = new Date(String(monthNum+1)+"/0/"+String(yearNum));
	numbDays = lastDate.getDate();
	firstDate = new Date(String(monthNum)+"/1/"+String(yearNum));
	firstDay = firstDate.getDay() + 1;
	createCalendar();
	return;
}


function easter(year) {

var a = year % 19;
var b = Math.floor(year/100);
var c = year % 100;
var d = Math.floor(b/4);
var e = b % 4;
var f = Math.floor((b+8) / 25);
var g = Math.floor((b-f+1) / 3);
var h = (19*a + b - d - g + 15) % 30;
var i = Math.floor(c/4);
var j = c % 4;
var k = (32 + 2*e + 2*i - h - j) % 7;
var m = Math.floor((a + 11*h + 22*k) / 451);
var month = Math.floor((h + k - 7*m + 114) / 31);
var day = ((h + k - 7*m +114) % 31) + 1;
eastermonth = month;
easterday = day;
}


function createCalendar() {
	calendarString = '';
	var daycounter = 0;
	calendarString += '<table width="183" border="0" cellpadding="0" cellspacing="0">';
	calendarString += '<tr>';
	calendarString += '<td align=\"center\" valign=\"center\" width=\"20\" height=\"30\">&nbsp;<\/td>';
	calendarString += '<td align=\"center\" valign=\"center\" width=\"22\" height=\"30\"><a href=\"#\" onMouseOver=\"document.PrevMo.src=\'images\/PrevMoOn40x40\.jpg\';\" onMouseOut=\"document.PrevMo.src=\'images\/PrevMoOff40x40\.jpg\';\" onClick=\"changedate(\'prevmo\')\"><img name=\"PrevMo\" src=\"images\/PrevMoOff40x40\.jpg\" width=\"8\" height=\"11\" border=\"0\" alt=\"Prev Mo\"\/><\/a><\/td>';
	calendarString += '<td  align=\"center\" valign=\"center\" width=\"130\" height=\"30\" colspan=\"3\"><b>' + wordMonth[monthNum-1] + '<\/b><\/td>';
	calendarString += '<td align=\"center\" valign=\"center\" width=\"20\" height=\"30\"><a href=\"#\" onMouseOver=\"document.NextMo.src=\'images\/NextMoOn40x40\.jpg\';\" onMouseOut=\"document.NextMo.src=\'images\/NextMoOff40x40\.jpg\';\" onClick=\"changedate(\'nextmo\')\"><img name=\"NextMo\" src=\"images\/NextMoOff40x40\.jpg\" width=\"8\" height=\"11\" border=\"0\" alt=\"Next Mo\"\/><\/a><\/td>';
	calendarString += '<\/tr>';
	calendarString += '<tr >';
	calendarString += '<td bgcolor=\"#ffffff\" class=\"boldText\" align=\"center\" valign=\"center\" width=\"20\" height=\"22\">S<\/td>';
	calendarString += '<td bgcolor=\"#ffffff\" align=\"center\" class=\"boldText\"  valign=\"center\" width=\"20\" height=\"22\">M<\/td>';
	calendarString += '<td bgcolor=\"#ffffff\" align=\"center\" class=\"boldText\"  valign=\"center\" width=\"20\" height=\"22\">T<\/td>';
	calendarString += '<td bgcolor=\"#ffffff\" align=\"center\" class=\"boldText\"  valign=\"center\" width=\"20\" height=\"22\">W<\/td>';
	calendarString += '<td bgcolor=\"#ffffff\" align=\"center\" class=\"boldText\"  valign=\"center\" width=\"20\" height=\"22\">T<\/td>';
	calendarString += '<td bgcolor=\"#ffffff\" align=\"center\" class=\"boldText\"  valign=\"center\" width=\"20\" height=\"22\">F<\/td>';
	calendarString += '<td bgcolor=\"#ffffff\" align=\"center\" class=\"boldText\"  valign=\"center\" width=\"20\" height=\"22\">S<\/td>';
	calendarString += '<\/tr>';

	thisDate == 1;
	
	for (var i = 1; i <= 6; i++) {
		calendarString += '<tr>';
		for (var x = 1; x <= 7; x++) {
			daycounter = (thisDate - firstDay)+1;
			thisDate++;
			if ((daycounter > numbDays) || (daycounter < 1)) {
				calendarString += '<td align=\"center\" class=\"otherDayClass \" height=\"30\" width=\"40\">&nbsp;<\/td>';
			} else {
				if (checkevents(daycounter,monthNum,yearNum,i,x) || ((todaysDay == x) && (todaysDate == daycounter) && (todaysMonth == monthNum))){
					if ((todaysDay == x) && (todaysDate == daycounter) && (todaysMonth == monthNum)) {
						calendarString += '<td align=\"center\" class=\"todayClass \" height=\"30\" width=\"20\"><a href="#">'+daycounter+'</a><\/td>';
					}
 					else	calendarString += '<td align=\"center\" class=\"otherDayClass \" height=\"30\" width=\"20\"><a href="#">'+ daycounter +'</a><\/td>';
				} else {
					calendarString += '<td align=\"center\" class=\"otherDayClass \" height=\"30\" width=\"20\"><a href="#">' + daycounter + '</a><\/td>';
				}
			}
		}
	
		calendarString += '<\/tr>';
	}

	calendarString += '<tr><td colspan=\"7\" nowrap align=\"center\" valign=\"center\" class=\" bigboldText\"  bgcolor=\"#fefefe\" width=\"150\" height=\"22\"><em>'+ yearNum +'</em><\/td><\/tr><\/table>';

	var object=document.getElementById('calendar');
	object.innerHTML= calendarString;
	thisDate = 1;
}


function checkevents(day,month,year,week,dayofweek) {
var numevents = 0;
var floater = 0;

	for (var i = 0; i < events.length; i++) {
		if (events[i][0] == "W") {
			if ((events[i][2] == dayofweek)) numevents++;
		}
		else if (events[i][0] == "Y") {
			if ((events[i][2] == day) && (events[i][1] == month)) numevents++;
		}
		else if (events[i][0] == "F") {
			if ((events[i][1] == 3) && (events[i][2] == 0) && (events[i][3] == 0) ) {
				easter(year);
				if (easterday == day && eastermonth == month) numevents++;
			} else {
				floater = floatingholiday(year,events[i][1],events[i][2],events[i][3]);
				if ((month == 5) && (events[i][1] == 5) && (events[i][2] == 4) && (events[i][3] == 2)) {
					if ((floater + 7 <= 31) && (day == floater + 7)) {
						numevents++;
					} else if ((floater + 7 > 31) && (day == floater)) numevents++;
				} else if ((events[i][1] == month) && (floater == day)) numevents++;
			}
		}
		else if ((events[i][2] == day) && (events[i][1] == month) && (events[i][3] == year)) {
			numevents++;
		}
	}

	if (numevents == 0) {
		return false;
	} else {
		return true;
	}
}


function showevents(day,month,year,week,dayofweek) {
var theevent = "";
var floater = 0;

	for (var i = 0; i < events.length; i++) {
		// First we'll process recurring events (if any):
		if (events[i][0] != "") {
			if (events[i][0] == "D") {
			}
			if (events[i][0] == "W") {
				if ((events[i][2] == dayofweek)) {
				theevent += "Events of: \n" + month +'/'+ day +'/'+ year + '\n';
				theevent += events[i][6] + '\n';
				theevent += 'Start Time: ' + events[i][4] + '\n';
				theevent += 'Ending Time: ' + events[i][5] + '\n';
				theevent += 'Description: ' + events[i][7] + '\n';
				theevent += '\n -------------- \n\n';
				document.forms.eventform.eventlist.value = theevent;
				}
			}
			if (events[i][0] == "M") {
			}
			if (events[i][0] == "Y") {
				if ((events[i][2] == day) && (events[i][1] == month)) {
				theevent += "Events of: \n" + month +'/'+ day +'/'+ year + '\n';
				theevent += events[i][6] + '\n';
				theevent += 'Start Time: ' + events[i][4] + '\n';
				theevent += 'Ending Time: ' + events[i][5] + '\n';
				theevent += 'Description: ' + events[i][7] + '\n';
				theevent += '\n -------------- \n\n';
				document.forms.eventform.eventlist.value = theevent;
				}
			}
			if (events[i][0] == "F") {
				if ((events[i][1] == 3) && (events[i][2] == 0) && (events[i][3] == 0) ) {
					if (easterday == day && eastermonth == month) {
						theevent += "Events of: \n" + month +'/'+ day +'/'+ year + '\n';
						theevent += events[i][6] + '\n';
						theevent += 'Start Time: ' + events[i][4] + '\n';
						theevent += 'Ending Time: ' + events[i][5] + '\n';
						theevent += 'Description: ' + events[i][7] + '\n';
						theevent += '\n -------------- \n\n';
						document.forms.eventform.eventlist.value = theevent;
					} 
				} else {
					floater = floatingholiday(year,events[i][1],events[i][2],events[i][3]);

					if ((month == 5) && (events[i][1] == 5) && (events[i][2] == 4) && (events[i][3] == 2)) {
						if ((floater + 7 <= 31) && (day == floater + 7)) {
							theevent += "Events of: \n" + month +'/'+ day +'/'+ year + '\n';
							theevent += events[i][6] + '\n';
							theevent += 'Start Time: ' + events[i][4] + '\n';
							theevent += 'Ending Time: ' + events[i][5] + '\n';
							theevent += 'Description: ' + events[i][7] + '\n';
							theevent += '\n -------------- \n\n';
							document.forms.eventform.eventlist.value = theevent;
						} else if ((floater + 7 > 31) && (day == floater)) {
							theevent += "Events of: \n" + month +'/'+ day +'/'+ year + '\n';
							theevent += events[i][6] + '\n';
							theevent += 'Start Time: ' + events[i][4] + '\n';
							theevent += 'Ending Time: ' + events[i][5] + '\n';
							theevent += 'Description: ' + events[i][7] + '\n';
							theevent += '\n -------------- \n\n';
							document.forms.eventform.eventlist.value = theevent;
						}
					} else if ((events[i][1] == month) && (floater == day)) {
						theevent += "Events of: \n" + month +'/'+ day +'/'+ year + '\n';
						theevent += events[i][6] + '\n';
						theevent += 'Start Time: ' + events[i][4] + '\n';
						theevent += 'Ending Time: ' + events[i][5] + '\n';
						theevent += 'Description: ' + events[i][7] + '\n';
						theevent += '\n -------------- \n\n';
						document.forms.eventform.eventlist.value = theevent;
					}
				}
		}
		}
		// Now we'll process any One Time events happening on the matching month, day, year:
		else if ((events[i][2] == day) && (events[i][1] == month) && (events[i][3] == year)) {
			theevent += "Events of: \n" + month +'/'+ day +'/'+ year + '\n';
			theevent += events[i][6] + '\n';
			theevent += 'Start Time: ' + events[i][4] + '\n';
			theevent += 'Ending Time: ' + events[i][5] + '\n';
			theevent += 'Description: ' + events[i][7] + '\n';
			theevent += '\n -------------- \n\n';
			document.forms.eventform.eventlist.value = theevent;
		}
	}
	if (theevent == "") document.forms.eventform.eventlist.value = 'No events to show.';
}


function floatingholiday(targetyr,targetmo,cardinaloccurrence,targetday) {

var firstdate = new Date(String(targetmo)+"/1/"+String(targetyr));	// Object Storing the first day of the current month.
var firstday = firstdate.getUTCDay();	// The first day (0-6) of the target month.
var dayofmonth = 0;	

	targetday = targetday - 1;

	if (targetday >= firstday) {
		cardinaloccurrence--;	
		dayofmonth = (cardinaloccurrence * 7) + ((targetday - firstday)+1);
	} else {
		dayofmonth = (cardinaloccurrence * 7) + ((targetday - firstday)+1);
	}
return dayofmonth;
}