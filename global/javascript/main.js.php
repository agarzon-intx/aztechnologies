/*****************************************************************************************************************
**************************************************General*********************************************************
*****************************************************************************************************************/

var nIntervId;
var nIntervAlert;
var mainLoadingCount = 0;
var pendingAlerts = [];
var pendingAlertsFlushed = false;
var AJAX_TIMEOUT_MS = 20000;

function isNumberKey(evt){
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	return true;
}

function mainLoadingOn(){
	mainLoadingCount++;
	$('#mainLoading').css("z-index", "99999");
	$("#mainLoading").css("display", "block");
}

function mainLoadingOff(){
	if (mainLoadingCount > 0) {
		mainLoadingCount--;
	}
	if (mainLoadingCount <= 0) {
		mainLoadingCount = 0;
		$('#mainLoading').css("z-index", "-1");
		$("#mainLoading").css("display", "none");
	}
}

function queueAlert(alertId){
	pendingAlerts.push(alertId);
	if (pendingAlertsFlushed) {
		flushPendingAlerts();
	}
}

function flushPendingAlerts(){
	pendingAlertsFlushed = true;
	if (!pendingAlerts.length) {
		return;
	}
	var alerts = pendingAlerts.slice();
	pendingAlerts = [];
	for (var i = 0; i < alerts.length; i++) {
		loadAlert(alerts[i]);
	}
}

var urlTarget = "";
		
function closeDiv(){
	$("#Avisos").css("display", "none");
}

function checkSessionExpire(){
	//console.log('checkSessionExpire');
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/sessionExpiredCheck.php',
		timeout: AJAX_TIMEOUT_MS,
		success: function (res) {
			if (res.status === '0') {
				alert(res.message.replace(/\\n/g, "\n").replace(/<br\s*\/?>/gi, "\n"));
				logout();
			}
			if (res.status === '1') {
				var d = new Date();
  				var n = d.toString();
				//console.log('Valid Session, Time ="' + n + '", ' + res.message);
			}
			if (res.status === '2') {
				
				//console.log('No session');
			}
		},
		error: function(jqxhr, status, exception) {
			// Background poll: never interrupt the user with an alert, just log it.
			console.log('checkSessionExpire failed: ' + status + ' ' + exception);
		}
	});
}


/*****************************************************************************************************************
**************************************************General*********************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
***********************************************TOP Components*****************************************************
*****************************************************************************************************************/
function loadTournament(Season){
    //console.log('loadTournament Season = ' + Season);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Top/changeTournament.php',
		data: {Season: Season},
		timeout: AJAX_TIMEOUT_MS,
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1' && res.category) {
				$("#categorysel").html(res.dataCategories);
				$("#teamLogos").html(res.dataLogos);
				loadCategory(Season, res.category);
				loadTournamentReloadList(Season);
			} else {
				flushPendingAlerts();
				alert(MSG_AJAX_GENERIC);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			flushPendingAlerts();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadTournamentReloadList(Season){
    //console.log('loadTournamentReloadList Season = ' + Season);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Top/changeTournamentReloadList.php',
		data: {Season: Season},
		success: function (res) {
			mainLoadingOff()
			$("#seasonsel").html(res.dataSeason);
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadCategory(Season, Category){
    //console.log('loadCategory Season = ' + Season + ', Category = ' + Category);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Top/changeCategory.php',
		data: {Season: Season, Category: Category},
		timeout: AJAX_TIMEOUT_MS,
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#teamLogos").html(res.dataLogos);
				$("#menuteams").html(res.menulogos);
				loadWeeks(Season, Category);
				loadCategoryReloadList(Season, Category)
			} else {
				flushPendingAlerts();
				alert(MSG_AJAX_GENERIC);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			flushPendingAlerts();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadCategoryReloadList(Season, Category){
    //console.log('loadCategoryReloadList Season = ' + Season + ', Category = ' + Category);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Top/changeCategoryReloadList.php',
		data: {Season: Season, Category: Category},
		success: function (res) {
			mainLoadingOff()
			$("#categorysel").html(res.dataCategories);
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadLanguage(language, url){
    //console.log('loadLanguage language = ' + language);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Top/changeLanguage.php',
		data: {language: language},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				window.location.href=url;
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadMenu(){
    //console.log('loadMenu');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Top/reloadMenu.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#sidenav-collapse-main").html(res.dataMenu);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function reloadNotifications(){
    //console.log('loadNotifications');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Top/reloadNotifications.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				if(res.totAlert > 0){
					$("#notificationssec").css('display', 'block');
					$("#notificationssec").html(res.dataAlert);
				}else{
					$("#notificationssec").css('display', 'none');
					$("#notificationssec").html('');
				}
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	
}
/*****************************************************************************************************************
***********************************************TOP Components*****************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
*************************************************Load Weeks*******************************************************
*****************************************************************************************************************/

function loadWeeks(Season, Category){
    //console.log('loadWeeks');
	if (typeof Season === 'undefined' || Season === null || Season === '') {
		var $seasonHidden = $('#selectedSeason');
		Season = $seasonHidden.length ? $seasonHidden.val() : '';
	}
	if (typeof Category === 'undefined' || Category === null || Category === '') {
		var $catHidden = $('#selectedCategory');
		Category = $catHidden.length ? $catHidden.val() : '';
	}
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Content/changeWeeks.php',
		data: {Season: Season, Category: Category},
		timeout: AJAX_TIMEOUT_MS,
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataWeeks);
			} else {
				alert(MSG_AJAX_GENERIC);
			}
			flushPendingAlerts();
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			flushPendingAlerts();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeek(Week){
    if(Week < 0){
    	mainLoadingOn();
    	$.ajax({
    		type: 'POST',
    		dataType: 'json',
    		url: 'ajax/Content/changeWeek0.php',
    		data: {Week: Week},
    		success: function (res) {
    			mainLoadingOff()
    			if (res.status === '1') {
    				$("#weekContent").html(res.dataWeek);
    			}else{
    				console.log(res);
    			}
    		},
    		error: function(jqxhr, status, exception) {
    			mainLoadingOff();
    			alert(MSG_AJAX_GENERIC);
    			console.log('Exception:' + exception);
    		}
    	});
    }else{
        //console.log('loadWeek week = ' + Week);
    	mainLoadingOn();
    	$.ajax({
    		type: 'POST',
    		dataType: 'json',
    		url: 'ajax/Content/changeWeek.php',
    		data: {Week: Week},
    		success: function (res) {
    			mainLoadingOff()
    			if (res.status === '1') {
    				$("#weekContent").html(res.dataWeek);
    				$("#weekTabContent").html(res.dataWeekTab);
    				loadWeekReloadList(Week);
    			}else{
    				console.log(res);
    			}
    		},
    		error: function(jqxhr, status, exception) {
    			mainLoadingOff();
    			alert(MSG_AJAX_GENERIC);
    			console.log('Exception:' + exception);
    		}
    	});
    }
}

function loadWeekReloadList(Week){
    //console.log('loadWeekReloadList week = ' + Week);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Content/changeWeeksReloadWeekSelector.php',
		data: {Week: Week},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#weekselectorsection").html(res.dataWeeks);
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

/*****************************************************************************************************************
*************************************************Load Weeks*******************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
**********************************************Login/Loogout*******************************************************
*****************************************************************************************************************/
function showLogin(){
	//console.log('showLogin');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Top/showLogin.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataLogin);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function showRegCode(){
    //console.log('showRegCode');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Login/showRegCode.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataBrowserReg);
				var $root = $("#body");
				var $inp = $root.find("#regCodeInput");
				$inp.off("keydown.regCodeSubmit").on("keydown.regCodeSubmit", function (e) {
					if (e.key === "Enter" || e.keyCode === 13) {
						e.preventDefault();
						e.stopPropagation();
						$root.find("#regCodeSubmitBtn").trigger("click");
					}
				});
				$inp.trigger("focus");
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function submitRegCode(code, message){
    //console.log('showRegCode');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Login/confirmRegCode.php',
		data: {code: code},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
			    alert(message);
				showLogin();
			}else{
			    $('#errorLoginL').html(res.dataError)
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function login(user, password, CSRFtoken){
    // console.log('login user = ' + user + ', password = ' + password + ', CSRFtoken = ' + CSRFtoken);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Login/login.php',
		data: {username: user, password: password, CSRFtoken: CSRFtoken},
		success: function (res) {
			mainLoadingOff()
			if (res.status == '0') {
				$("#errorLoginL").html(res.dataError);
			}
			if (res.status == '1') {
				nIntervId = setInterval(checkSessionExpire, 60000);
				loadMenu();
				loadWeeks();
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Status: ' + status);
			console.log('Exception: ' + exception);
			console.log('jqxhr: ' + jqxhr);
		}
	});
}

function logout(){
    //console.log('logout');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Login/logout.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				clearInterval(nIntervId);
				loadMenu();
				loadWeeks();
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function showBrowserRegister(){
    //console.log('showBrowserRegister');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Login/showBrowserReg.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataBrowserReg);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function browserRegister(email){
    //console.log('browserRegister');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Login/browserReg.php',
		data: {email: email},
		success: function (res) {
			mainLoadingOff()
			if (res.status == '0') {
				$("#errorLoginL").html(res.dataError);
			}
			if (res.status == '1') {
				showRegCode();
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

/*****************************************************************************************************************
**********************************************Login/Loogout*******************************************************
*****************************************************************************************************************/
function onChangeWeek(Week){
	loadWeek(Week);								  
}

function loadTeam(team,season){
	//console.log('loadTeam team = ' + team + ', season = ' + season);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Content/changeTeam.php',
		data: {team: team, season:season},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataTeam);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function changeTeamTab(id){
	var currentAttrValue = id;

	// Show/Hide Tabs
	jQuery(currentAttrValue).siblings().slideUp(800);
	jQuery(currentAttrValue).delay(800).slideDown(800);				 

	// Change/remove current tab to active
	$(id+'li').addClass('active').siblings().removeClass('active');

}
									  
function changeStatusTab(id){
	//console.log('changeStatusTab id = ' + id);
	var currentAttrValue = id;

	// Show/Hide Tabs
	jQuery(currentAttrValue).siblings().slideUp(800);
	jQuery(currentAttrValue).delay(800).slideDown(800);				 

	// Change/remove current tab to active
	$(id+'li').addClass('active').siblings().removeClass('active');
	if(id == '#jugadores'){
		setTimeout(() => { 		
			var element = document.getElementById('teamplayersl');
			var elementHeigth = element.getElementsByClassName('active')[0].offsetHeigth + 'px';
			var elementWidth = element.getElementsByClassName('active')[0].offsetWidth + 'px';
			element.getElementsByClassName('moving-tab')[0].style.height = elementHeigth;
			element.getElementsByClassName('moving-tab')[0].style.width = elementWidth; 
		}, 1000);
	}
}
									  
function changeTeamRosterTab(id){
		var currentAttrValue = id;

		// Show/Hide Tabs
		jQuery(currentAttrValue).siblings().slideUp(800);
		jQuery(currentAttrValue).delay(800).slideDown(800);				 

		// Change/remove current tab to active
		$(id+'li').addClass('active').siblings().removeClass('active');
}

function fixListSchedule(id){
	//console.log("fixListSchedule() => " + id);
									  
	var index = 1;
	$('#'+id+'').find('.tWrap__body').find('thead').find('tr:first').find('th').each(function(){
		$('#'+id+' .tWrap__head').find('thead').find('tr:first').find('th:nth-child('+index+')').css( "width" ,$(this).get(0).getBoundingClientRect().width + "px");
		index = index +1;
	});
	
	var index = 1;
	$('#'+id+'').find('.tWrap__body').find('thead').find('tr:nth-child(2)').find('th').each(function(){
		$('#'+id+' .tWrap__head').find('thead').find('tr:nth-child(2)').find('th:nth-child('+index+')').css( "width" ,$(this).get(0).getBoundingClientRect().width + "px");
		index = index +1;
	});
	
	index = 1;
	$('#'+id+'').find('.tWrap__body').find('tbody').find('tr:nth-child(1)').find('td').each(function(){
		$(this).css( "width" , $('#'+id+' .tWrap__body').find('tbody').find('tr').find('td:nth-child('+index+')').get(0).getBoundingClientRect().width + "px");
		index = index +1;
	});
									  
	$('#'+id+'').find('.tWrap__body').find('thead').toggle();
									  
	$('#'+id+'').find('.tWrap__head').find('tbody').toggle();
}
									  
function fixListScheduleWeek(id){
	//console.log("fixListSchedule() => " + id);
									  
	var index = 1;
	$('#'+id+'').find('.tWrap__body').find('thead').find('tr:first').find('th').each(function(){
		$('#'+id+' .tWrap__head').find('thead').find('tr:first').find('th:nth-child('+index+')').css( "width" ,$(this).get(0).getBoundingClientRect().width + "px");
		index = index +1;
	});
	
	$('#'+id+'').find('.tWrap__body').find('thead').toggle();
}
									  
function fixList(id){
	//console.log("fixList() => " + id);
									  
	var index = 1;
	$('#'+id+'').find('.tWrap__body').find('thead').find('tr').find('th').each(function(){
		$('#'+id+' .tWrap__head').find('thead').find('tr').find('th:nth-child('+index+')').css( "width" ,$(this).get(0).getBoundingClientRect().width + "px");
		//console.log($(this)[0].getBoundingClientRect().width + ' vs '+ $('.tWrap__head').find('thead').find('tr').find('th:nth-child('+index+')')[0].getBoundingClientRect().width);
		index = index +1;
	});
	
	$('#'+id+'').find('.tWrap__body').find('thead').toggle();
	
	index = 1;
	$('#'+id+'').find('.tWrap__head').find('thead').find('tr').find('th').each(function(){
		$('#'+id+' .tWrap__body').find('tbody').find('tr').find('td:nth-child('+index+')').css( "width" ,$(this).get(0).getBoundingClientRect().width + "px");
		//console.log($(this)[0].getBoundingClientRect().width + ' vs '+ $('.tWrap__head').find('thead').find('tr').find('th:nth-child('+index+')')[0].getBoundingClientRect().width);
		index = index +1;
	});
}
									  
function previewPlayerShow(playerID){
	//console.log('previewPlayerShow playerID = ' + playerID + ', season = ' + season);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Content/team-PlayersPlayerPreview.php',
		data: {playerID: playerID},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#jugador").css("display", "grid");
				$("#teamPlayerPreview").html(res.dataTeamPlayerPreview);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}
									  
function previewPlayerShowVoleibol(playerID){
	//console.log('previewPlayerShowVoleibol playerID = ' + playerID + ', season = ' + season);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Content/team-PlayersPlayerPreviewVoleibol.php',
		data: {playerID: playerID},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#jugador").css("display", "grid");
				$("#teamPlayerPreview").html(res.dataTeamPlayerPreview);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function previewPlayerHide(){
	$("#jugador").css("display", "none");
}
									  
function loadAlert(alert){
	//console.log('loadAlert');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Alert/loadAlert.php',
		data: {alert: alert},
		success: function (res) {
			mainLoadingOff();
			if (res.status === '1') {
				$("#alertaContent").html(res.dataAlert);
				$('#alertaContent').css("z-index", "99999");
	            $("#alertaContent").css("display", "block");
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function closeAlert(){
	$('#alertaContent').css("z-index", "-1");
    $("#alertaContent").css("display", "none");
}

/* AzMarcador: scoped scoreboard widget — loads assets only when a host is present */
var AZ_MARCADOR_VER = '20260726c';
var AZ_MARCADOR_CSS = './css/az-marcador.css?v=' + AZ_MARCADOR_VER;
var AZ_MARCADOR_JS = './javascript/az-marcador.js?v=' + AZ_MARCADOR_VER;
var AZ_MARCADOR_FONTS = 'https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600&family=Syne:wght@700;800&family=Teko:wght@500;600;700&display=swap';

function ensureAzMarcadorAssets(done) {
	if (!document.getElementById('az-marcador-fonts')) {
		var fonts = document.createElement('link');
		fonts.id = 'az-marcador-fonts';
		fonts.rel = 'stylesheet';
		fonts.href = AZ_MARCADOR_FONTS;
		document.head.appendChild(fonts);
	}
	if (!document.getElementById('az-marcador-css')) {
		var link = document.createElement('link');
		link.id = 'az-marcador-css';
		link.rel = 'stylesheet';
		link.href = AZ_MARCADOR_CSS;
		document.head.appendChild(link);
	}
	if (window.AzMarcador && typeof window.AzMarcador.mount === 'function') {
		done();
		return;
	}
	var existing = document.getElementById('az-marcador-js');
	if (existing) {
		existing.addEventListener('load', done);
		return;
	}
	var script = document.createElement('script');
	script.id = 'az-marcador-js';
	script.src = AZ_MARCADOR_JS;
	script.onload = done;
	script.onerror = function () {
		console.log('AzMarcador failed to load');
	};
	document.head.appendChild(script);
}

function azMountMarcadoresIn(selector) {
	var $hosts = $(selector).find('[data-az-marcador]');
	if (!$hosts.length) return;
	ensureAzMarcadorAssets(function () {
		if (!window.AzMarcador || typeof window.AzMarcador.mount !== 'function') return;
		$hosts.each(function () {
			window.AzMarcador.mount(this);
		});
	});
}

function setWeekGameDetailHtml(selector, html) {
	$(selector).html(html);
	azMountMarcadoresIn(selector);
	// jQuery may eval inline scripts, but always (re)bind pill tabs after AJAX insert.
	$(selector).find('ul.nav-pills[id]').each(function () {
		if (typeof window.initNavs === 'function') {
			window.initNavs(this.id);
		}
	});
}

function abrirFicha(id, week, game, gamedesc, lgoals, vgoals){
	//console.log('abrirFicha');
	var attr = $('#'+id).attr('style');
	$('.juego').css('display', 'none');
	$('#expandir'+id).attr('src', './imagenes/expandir.png');	
	$('.expandirButton').attr('src', './imagenes/expandir.png');
	// For some browsers, `attr` is undefined; for others,
	// `attr` is false.  Check for both.
	if (typeof attr == typeof undefined) {
		$('#'+id).css('display', 'none');					
		$('#expandir'+id).attr('src', './imagenes/expandir.png');
		$("#content" + id).html('');

	}else{
		$('#'+id).removeAttr('style');	
		$('#expandir'+id).attr('src', './imagenes/colapsar.png');
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Content/week-ScheduleScoresGameDetail.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals},
			success: function (res) {
				mainLoadingOff()
				if (res.status === '1') {
					setWeekGameDetailHtml("#content" + id, res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
				console.log('Exception:' + exception);
			}
		});
	}
}

function abrirFichaS(id, week, game, gamedesc, lgoals, vgoals){
	//console.log('abrirFicha');
	var attr = $('#'+id + 'S').attr('style');
	$('.juegoS').css('display', 'none');
	$('#expandir'+id+'S').attr('src', './imagenes/expandir.png');	
	$('.expandirButtonS').attr('src', './imagenes/expandir.png');
	// For some browsers, `attr` is undefined; for others,
	// `attr` is false.  Check for both.
	if (typeof attr == typeof undefined) {
		$('#'+id+'S').css('display', 'none');					
		$('#expandir'+id+'S').attr('src', './imagenes/expandir.png');
		$("#content" + id+'S').html('');

	}else{
		$('#'+id+'S').removeAttr('style');	
		$('#expandir'+id+'S').attr('src', './imagenes/colapsar.png');
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Content/week-ScheduleScoresGameDetailS.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals},
			success: function (res) {
				mainLoadingOff()
				if (res.status === '1') {
					setWeekGameDetailHtml("#content" + id + 'S', res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
				console.log('Exception:' + exception);
			}
		});
	}
}

/*****************************************************************************************************************
********************************************User MAnagement*******************************************************
*****************************************************************************************************************/
var userManagementFilterTimer = null;

function userManagementFilterListDebounced() {
	if (userManagementFilterTimer) {
		clearTimeout(userManagementFilterTimer);
	}
	userManagementFilterTimer = setTimeout(function () {
		userManagementReloadList();
	}, 350);
}

function userManagementReloadList() {
	var filterVal = '';
	var $input = $('#userListFilter');
	if ($input.length) {
		filterVal = $input.val();
	}
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Login/userManagementReloadList.php',
		data: { userListFilter: filterVal },
		success: function (res) {
			if (res.status === '1') {
				var $wrap = $('#userManagementListTables');
				if ($wrap.length) {
					$wrap.replaceWith(res.dataUserList);
				}
			}
		},
		error: function (jqxhr, status, exception) {
			console.log('Exception:' + exception);
		}
	});
}

function userManagementShow(){
	//console.log('loadUsersAdmin');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Login/userManagement.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataUser);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function userManagementLoadImage(id, idl, logo){
	var index = document.getElementById(id).selectedIndex;
	var optionsl = document.getElementById(idl).options;
	document.getElementById(logo).src = "imagenes/"+optionsl[index].text+".png";			
}

function userManagementLimpiarCreateUser(image){
	document.getElementById("equipo").selectedIndex = 0;
	document.getElementById("logoE").src = "imagenes/" + image + ".png";
	document.getElementById("error").innerHTML = "";
	document.getElementById("nombre").disabled = false;
	document.getElementById("apellidop").disabled = false;
	document.getElementById("apellidom").disabled = false;
	document.getElementById("telefono").disabled = false;
	document.getElementById("email").disabled = false;
	document.getElementById("equipo").disabled = false;
	document.getElementById("usuario").disabled = false;
	document.getElementById("password_id").disabled = false;

	document.getElementById("register_email_errorloc").innerHTML = "";
	document.getElementById("register_nombre_errorloc").innerHTML = "";
	document.getElementById("register_apellidop_errorloc").innerHTML = "";
	document.getElementById("register_apellidom_errorloc").innerHTML = "";
	document.getElementById("register_telefono_errorloc").innerHTML = "";
	document.getElementById("nombre").value = "";
	document.getElementById("apellidop").value = "";
	document.getElementById("apellidom").value = "";
	document.getElementById("telefono").value = "";
	document.getElementById("email").value = "";
	document.getElementById("usuario").value = "";
	document.getElementById("password_id").value = "";
	$("#register_errorloc").html("");
	$('#userManagementCreate').toggle(); 
	$('#userManagementList').toggle();			
}

function userManagementLimpiarEditUser(){
	$("#userManagementEdit").html("");
	$('#userManagementEdit').toggle(); 
	$('#userManagementList').toggle();			
}

function playersManagementAdminValidateAll(cat, team){
    //console.log('playersManagementAdminValidateAll cat = ' + cat + ', team = ' + team);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Admin/playersManagementValidateAll.php',
		data: {team: team},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert('Validacion Terminada');
				playersManagementAdminCategoryTeamShowReloadList(cat, team);; 
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});	
}

function userManagementCreateUser(name, lastname, lastname2, phone, email, user, password, salt){
	//console.log('userManagementCreateUser name = ' + name + ', lastname = ' + lastname + ', lastname2 = ' + lastname2 + ', phone = ' + phone + ', email = ' + email + ', team = ' + team + ', user = ' + user + ', password = ' + password + ', salt = ' + salt);
	var equipos = '';
	$("#equipor option").each(function(){
        equipos = equipos + $(this).val() + ',';
    });
    equipos = equipos.slice(0, -1);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Login/userManagementCreateSave.php',
		data: {nombre: name, apellidop: lastname, apellidom: lastname2, telefono: phone, email: email, equipo: equipos, username: user, password: password, salt: salt},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataUser);
				userManagementShow();
			}
			if (res.status === '0') {
				$("#register_errorloc").html(res.dataUser);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function userManagementDeleteUser(id){
	//console.log('userManagementDeleteUser id = ' + id);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Login/userManagementDelete.php',
		data: {id: id},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				//$("#register_errorloc").html(res.dataUser);
				alert(res.dataUser);
				userManagementShow();
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function userManagementReloadTeamLeftOptions($equipol, mode, editUserId, onDone) {
	mainLoadingOn();
	var payload = { mode: mode || 'create' };
	if (mode === 'edit' && editUserId) {
		payload.id = editUserId;
	}
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Login/userManagementTeamLeftOptions.php',
		data: payload,
		success: function (res) {
			mainLoadingOff();
			if (res.status === '1' && typeof res.optionsInnerHtml === 'string') {
				$equipol.html(res.optionsInnerHtml);
			}
			if (typeof onDone === 'function') {
				onDone();
			}
		},
		error: function (jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function userManagementNormSelectVal($el) {
	var v = $el.val();
	if (v === undefined || v === null) {
		return '';
	}
	return String(v);
}

/** Re-apply status/category visibility on #equipol without resetting dropdowns or re-binding events. */
function userManagementApplyTeamLeftFilters($root) {
	if (!$root || !$root.length) {
		return;
	}
	var $status = $root.find('.um-team-status-filter').first();
	var $cat = $root.find('.um-category-filter').first();
	var $equipol = $root.find('select#equipol').first();
	if (!$equipol.length || !$status.length || !$cat.length) {
		return;
	}
	var st = userManagementNormSelectVal($status);
	var cat = userManagementNormSelectVal($cat);
	$equipol.find('option').each(function () {
		var opt = this;
		var $o = $(opt);
		var v = userManagementNormSelectVal($o);
		if (v === '0' || v === '-1') {
			opt.removeAttribute('hidden');
			opt.disabled = false;
			return;
		}
		var act = $o.attr('data-activo');
		var cId = $o.attr('data-categoria');
		var okS = (st === '2' || st === String(act));
		var okC = (cat === '' || cat === String(cId));
		var show = okS && okC;
		if (show) {
			opt.removeAttribute('hidden');
			opt.disabled = false;
		} else {
			opt.setAttribute('hidden', 'hidden');
			opt.disabled = true;
			opt.selected = false;
		}
	});
}

function userManagementBindTeamLeftFilters($root) {
	if (!$root || !$root.length) {
		return;
	}
	var $status = $root.find('.um-team-status-filter').first();
	var $cat = $root.find('.um-category-filter').first();
	var $equipol = $root.find('select#equipol').first();
	if (!$equipol.length || !$status.length || !$cat.length) {
		return;
	}
	$status.val('2');
	$cat.val('');
	$status.add($cat).off('change.umTeamFilter input.umTeamFilter').on('change.umTeamFilter input.umTeamFilter', function () {
		userManagementApplyTeamLeftFilters($root);
	});
	userManagementApplyTeamLeftFilters($root);
	$root.find('.um-refresh-team-list').off('click.umTeamRefresh').on('click.umTeamRefresh', function () {
		var mode = $(this).data('mode') || 'create';
		var uid = $(this).data('user-id');
		userManagementReloadTeamLeftOptions($equipol, mode, uid, function () {
			userManagementBindTeamLeftFilters($root);
		});
	});
}

function userManagementLimpiarCreateUser(){
	$("#userManagementCreate").css('display', 'none');
	$("#userManagementList").css('display', 'block');
	$("#userManagementCreate").html('');
}

function userManagementShowAdd(){
	//console.log('userManagementShowAdd');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Login/userManagementCreate.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#userManagementCreate").css('display', 'block');
				$("#userManagementList").css('display', 'none');
				$("#userManagementCreate").html(res.dataUserAdd);
				setTimeout(function () {
					userManagementBindTeamLeftFilters($("#userManagementCreate"));
				}, 0);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function userManagementShowEdit(id){
	//console.log('userManagementShowEdit id = ' + id);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Login/userManagementEdit.php',
		data: {id: id},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#userManagementEdit").html(res.dataUser);
				setTimeout(function () {
					userManagementBindTeamLeftFilters($("#userManagementEdit"));
				}, 0);
				$('#userManagementEdit').toggle(); 
				$('#userManagementList').toggle();			
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function userManagementEditUser(userid, name, lastname, lastname2, phone, email, active, username){
	console.log('userManagementEditUser name = ' + name + ', lastname = ' + lastname + ', lastname2 = ' + lastname2 + ', phone = ' + phone + ', active = ' + active);
	var equipos = '';
	$("#equipor option").each(function(){
        equipos = equipos + $(this).val() + ',';
    });
    equipos = equipos.slice(0, -1);
	
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Login/userManagementEditSave.php',
		data: {userid: userid, nombre: name, apellidop: lastname, apellidom: lastname2, telefono: phone, email: email, equipo: equipos, active: active, username: username},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataUser);
				userManagementShow();
			}
			if (res.status === '0') {
				$("#register_errorloca").html(res.dataUser);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
								 
}

function userManagementShowResetPassword(){
	//console.log('userManagementShowResetPAssword');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Login/userManagementResetPassword.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataUser);		
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function userManagementShowResetPasswordEnterConfirmCode(email){
	//console.log('userManagementShowResetPasswordEnterConfirmCode');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Login/userManagementResetPasswordEnterConfirmCode.php',
		data: {email: email},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataUser);		
			}else{
				$("#resetreq_email_errorlocels").html(res.dataUser);		
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function userManagementResetPasswordEnterConfirmCodeSend(code, email){
	//console.log('userManagementResetPasswordEnterConfirmCodeSend');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Login/userManagementResetPasswordShowChangePassword.php',
		data: {code: code, email: email},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataUser);		
			}else{
				$("#register_code_error_reset_pwd").html(res.dataUser);		
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function userManagementResetPasswordChangePassword(code, password, salt){
	//console.log('userManagementResetPasswordEnterConfirmCodeSend code = ' + code + ', password = ' + password + ', salt = ' + salt);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Login/userManagementResetPasswordChangePassword.php',
		data: {code: code, password: password, salt: salt},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataUser);
				showLogin();
			}else{
				$("#register_code_error_reset_pwd").html(res.dataUser);		
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

/*****************************************************************************************************************
********************************************User MAnagement*******************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
*****************************************Player Management Admin**************************************************
*****************************************************************************************************************/
var catTeamAdmCat, catTeamAdmTeam;
function playersManagementAdminCategoryShow(){
	//console.log('playersManagementAdminCategoryShow');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Admin/playersManagementChangeCategories.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataCategory);
				playersManagementAdminCategoryTeamShow(res.category);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function playersManagementAdminCategoryShowReloadList(Category){
    //console.log('playersManagementAdminCategoryShowReloadList Category = ' + Category);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Admin/playersManagementChangeCategoriesReloadList.php',
		data: {Category: Category},
		success: function (res) {
			mainLoadingOff()
			$("#playerAdminContentCategoryList").html(res.dataCategories);
			playersManagementAdminCategoryTeamShow(Category);
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function playersManagementAdminCategoryTeamShow(Category){
	//console.log('playersManagementAdminCategoryTeamShow Category = ' + Category);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Admin/playersManagementChangeTeams.php',
		data: {Category: Category},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
			    $("#playerAdminContentTeamList").html(res.dataTeam);
				playersManagementAdminShow(Category, res.team);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function playersManagementAdminCategoryTeamShowReloadList(Category, Team){
    //console.log('playersManagementAdminCategoryTeamShowReloadList Category = ' + Category);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Admin/playersManagementChangeTeamsReloadList.php',
		data: {Category: Category, Team: Team},
		success: function (res) {
			mainLoadingOff();
			$("#playerAdminContentTeamList").html(res.dataCategories);
			playersManagementAdminShow(Category, Team);
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function playersManagementAdminShow(Category, Team){
    catTeamAdmCat = Category;
    catTeamAdmTeam = Team;
	console.log('playersManagementAdminShow Category = ' + Category + ', Team = ' + Team);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Admin/playersManagement.php',
		data: {Team: Team},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#teamContent").html(res.dataPlayer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function disableButton(btn){
	document.getElementsByName(btn).disabled = true;
}

function fireEvent(element,event) {
   if (document.createEvent) {
	   return !element.click();
   } else {
	   // dispatch for IE
	   var evt = document.createEventObject();
	   return element.fireEvent('on'+event,evt)
   }
};
		
function playerManagementLoadImage(id, logo){
	document.getElementById(logo).src = "imagenes/"+$('#'+id).val().split(",")[1]+".png";
}

function playersManagementAdminShowCreate(Category, Team){
	//console.log('playersManagementAdminShowCreate');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Admin/playersManagementCreate.php',
		data: {Category: Category, Team: Team},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#playersManagementCreate").toggle();
				$("#playersManagementList").toggle();
				$("#playersManagementCreate").html(res.dataPlayerAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function playersManagementAdminCreatePlayer(name, lastname, lastname2, nickname, birthdate, playernumber, phone, sex, email, id, comments, valid, status, team, picture, idf, idb, signature, type, scat, steam, idpdf){
	//console.log('playersManagementAdminCreatePlayer name = ' + name + ', lastname = ' + lastname + ', lastname2 = ' + lastname2 + ', nickname = ' + nickname + ', birthdate = ' + birthdate + ', playernumber = ' + playernumber + ', phone = ' + phone + ', sex = ' + sex + ', email = ' + email + ', id = ' + id + ', comments = ' + comments + ', valid = ' + valid + ', status = ' + status + ', team = ' + team + ', picture = ' + picture + ', idf = ' + idf + ', idb = ' + idb + ', signature = ' + signature + ', type = ' + type);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Admin/playersManagementCreateSave.php',
		data: {name: name, lastname: lastname, lastname2: lastname2, nickname: nickname, birthdate: birthdate, playernumber: playernumber, phone: phone, sex: sex, email: email, id: id, comments: comments, valid: valid, status: status, team: team, picture: picture, idf: idf, idb: idb, signature: signature, type: type, idpdf: idpdf},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataPlayerMessage);
				playersManagementAdminShow(catTeamAdmCat, catTeamAdmTeam);
			}else{
				alert(res.dataPlayerMessage);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});playersManagementAdminEditPlayer
}

function playersManagementAdminShowEdit(player){
	//console.log('playersManagementAdminShowEdit player = ' + player);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Admin/playersManagementEdit.php',
		data: {player: player},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#playersManagementEdit").toggle();
				$("#playersManagementList").toggle();
				$("#playersManagementEdit").html(res.dataPlayerEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function playersManagementAdminEditPlayer(playerid, name, lastname, lastname2, nickname, birthdate, playernumber, phone, sex, email, id, comments, valid, status, team, picture, idf, idb, signature, type, scat, steam, idpdf){
	console.log('playersManagementAdminEditPlayer playerid = ' + playerid + ', name = ' + name + ', lastname = ' + lastname + ', lastname2 = ' + lastname2 + ', nickname = ' + nickname + ', birthdate = ' + birthdate + ', playernumber = ' + playernumber + ', phone = ' + phone + ', sex = ' + sex + ', email = ' + email + ', id = ' + id + ', comments = ' + comments + ', valid = ' + valid + ', status = ' + status + ', team = ' + team + ', picture = ' + picture + ', idf = ' + idf + ', idb = ' + idb + ', signature = ' + signature + ', type = ' + type);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Admin/playersManagementEditSave.php',
		data: {playerid: playerid, name: name, lastname: lastname, lastname2: lastname2, nickname: nickname, birthdate: birthdate, playernumber: playernumber, phone: phone, sex: sex, email: email, id: id, comments: comments, valid: valid, status: status, team: team, picture: picture, idf: idf, idb: idb, signature: signature, type: type, idpdf: idpdf},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataPlayerMessage);
				playersManagementAdminShow(catTeamAdmCat, catTeamAdmTeam);
			}else{
				alert(res.dataPlayerMessage);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function playersManagementAdminShowPrintList(){
	//console.log('playersManagementAdminShowPrintList player = ' + player);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Admin/playersManagementGeneratePrintListShow.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataPrintList);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}
/*****************************************************************************************************************
*****************************************Player Management Admin**************************************************
*****************************************************************************************************************/


/*****************************************************************************************************************
*************************************************Referee Admin****************************************************
*****************************************************************************************************************/

function refereeManagementShow(){
	console.log('refereeManagementShow');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Referee/RefereeManagement.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.datareferee);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function refereeManagementAdminCreateReferee(name, lastname, lastname2, nickname, birthdate, phone, sex, email, id, comments ,record ,courses ,valid, status, picture, idf, idb, signature){
	//console.log('refereeManagementAdminCreatePlayer name = ' + name + ', lastname = ' + lastname + ', lastname2 = ' + lastname2 + ', nickname = ' + nickname + ', birthdate = ' + birthdate + ',  phone = ' + phone + ', sex = ' + sex + ', email = ' + email + ', id = ' + id + ', comments = ' + comments + ',record = ' + record + ',courses = ' + courses +, ' valid = ' + valid + ', status = ' + status + ',  picture = ' + picture + ', idf = ' + idf + ', idb = ' + idb + ', signature = ' + signature + ');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Referee/RefereeManagementCreateSave.php',
		data: {name: name, lastname: lastname, lastname2: lastname2, nickname: nickname, birthdate: birthdate, phone: phone, sex: sex, email: email, id: id, comments: comments, record: record ,courses: courses, valid: valid, status: status, picture: picture, idf: idf, idb: idb, signature: signature},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataRefereeMessage);
				refereeManagementShow( );
			}else{
				alert(res.dataRefereeMessage);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function refereeManagementShowAdd(){
	//console.log('refereeManagementShowAdd');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Referee/RefereeManagementCreate.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#refereeManagementCreate").css('display', 'block');
				$("#refereeManagementList").css('display', 'none');
				$("#refereeManagementCreate").html(res.refereeAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function refereeManagementHideAdd(){
	//console.log('alertManagementHideAdd');
	$("#refereeManagementCreate").css('display', 'none');
	$("#refereeManagementList").css('display', 'block');
	$("#refereeManagementCreate").html('');
}

function RefereeManagementCreateSave(Titulo, Fecha, editor, file){
	//console.log('RefereeManagementCreateSave Titulo = ' + Titulo + ', Fecha = ' + Fecha + ', editor = ' + editor + ', minuta = ' + minuta);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Referee/RefereeManagementNewSave.php',
		data: {Titulo: Titulo, Fecha: Fecha, editor: editor, file: file},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataRefereeAnswer);
				refereeManagementShow();
			}else{
				alert(res.dataRefereeAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function refereeManagementAdminEditReferee(refereeid, name, lastname, lastname2, nickname, birthdate, phone, sex, email, id, comments,record, courses, valid, status, picture, idf, idb, signature,){
	//console.log('playersManagementAdminEditPlayer refereeid = ' + refereeid + ', name = ' + name + ', lastname = ' + lastname + ', lastname2 = ' + lastname2 + ', nickname = ' + nickname + ', birthdate = ' + birthdate + ', phone = ' + phone + ', sex = ' + sex + ', email = ' + email + ', id = ' + id + ', comments = ' + comments + ', record = ' + reord + ', courses = ' + courses + ',  valid = ' + valid + ', status = ' + status + ', picture = ' + picture + ', idf = ' + idf + ', idb = ' + idb + ', signature = ' + signature + ');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Referee/RefereeManagementEditSave.php',
		data: {refereeid: refereeid, name: name, lastname: lastname, lastname2: lastname2, nickname: nickname, birthdate: birthdate, phone: phone, sex: sex, email: email, id: id, comments: comments, record: record, courses: courses, valid: valid, status: status, picture: picture, idf: idf, idb: idb, signature: signature},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataRefereeMessage);
				refereeManagementShow( );
			}else{
				alert(res.dataPlayerMessage);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function refereeManagementShowEdit(id){
	//console.log('RefereeManagementShowEdit week_id = ' + id);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Referee/RefereeManagementEdit.php',
		data: {referee: id},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#refereeManagementEdit").css('display', 'block');
				$("#refereeManagementList").css('display', 'none');
				$("#refereeManagementEdit").html(res.dataRefereeEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function refereeManagementHideEdit(){
	//console.log('refereeManagementHideEdit');
	$("#refereeManagementEdit").css('display', 'none');
	$("#refereeManagementList").css('display', 'block');
	$("#refereeManagementEdit").html('');
}

function refereeManagementEditSave(id, Titulo, Fecha, editor, file){
	//console.log('alertManagementEditSave id = ' + id + ', Titulo = ' + Titulo + ', Fecha = ' + Fecha + ', editor = ' + editor + ', minuta = ' + minuta);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Referee/RefereeManagementUpdateSave.php',
		data: {id: id, Titulo: Titulo, Fecha: Fecha, editor: editor, file: file},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataRefereeAnswer);
				refereeManagementShow();
			}else{
				alert(res.dataRefereeAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

/*****************************************************************************************************************
*************************************************Referee Admin****************************************************
*****************************************************************************************************************/


/*****************************************************************************************************************
*****************************************Player Management Team***************************************************
*****************************************************************************************************************/
var catTeamPlayerCat, catTeamPlayerTeam;
function playersManagementTeamCategoryShow(){
	//console.log('playersManagementTeamCategoryShow');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Team/playersManagementChangeCategories.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataCategory);
				playersManagementTeamCategoryTeamShow(res.category);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function playersManagementTeamCategoryShowReloadList(Category){
    //console.log('playersManagementTeamCategoryShowReloadList Category = ' + Category);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Team/playersManagementChangeCategoriesReloadList.php',
		data: {Category: Category},
		success: function (res) {
			mainLoadingOff()
			$("#playerTeamContentCategoryList").html(res.dataCategories);
			playersManagementTeamCategoryTeamShow(Category);
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function playersManagementTeamCategoryTeamShow(Category){
	//console.log('playersManagementTeamCategoryTeamShow Category = ' + Category);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Team/playersManagementChangeTeams.php',
		data: {Category: Category},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
			    $("#playerTeamContentTeamList").html(res.dataCatTeam);
				playersManagementTeamShow(Category, res.team);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function playersManagementTeamCategoryTeamShowReloadList(Category, Team){
    //console.log('playersManagementTeamCategoryTeamShowReloadList Category = ' + Category);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Team/playersManagementChangeTeamsReloadList.php',
		data: {Category: Category, Team: Team},
		success: function (res) {
			mainLoadingOff();
			$("#playerTeamContentTeamList").html(res.dataCategories);
			playersManagementTeamShow(Category, Team);
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function playersManagementTeamShow(Category, Team){
    catTeamPlayerCat= Category;
    catTeamPlayerTeam = Team;
	//console.log('playersManagementTeamShow Category = ' + Category + ', Team = ' + Team);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Team/playersManagement.php',
		data: {Team: Team},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#teamContent").html(res.dataPlayer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function playersManagementTeamShowCreate(Team){
	//console.log('playersManagementTeamShowCreate');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Team/playersManagementCreate.php',
		data: {Team: Team},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#playersManagementCreate").toggle();
				$("#playersManagementList").toggle();
				$("#playersManagementCreate").html(res.dataPlayerAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function playersManagementTeamCreatePlayer(name, lastname, lastname2, nickname, birthdate, playernumber, phone, sex, email, id, comments, valid, status, team, picture, idf, idb, signature, type, srcteam, idpdf){
	//console.log('playersManagementTeamCreatePlayer name = ' + name + ', lastname = ' + lastname + ', lastname2 = ' + lastname2 + ', nickname = ' + nickname + ', birthdate = ' + birthdate + ', playernumber = ' + playernumber + ', phone = ' + phone + ', sex = ' + sex + ', email = ' + email + ', id = ' + id + ', comments = ' + comments + ', valid = ' + valid + ', status = ' + status + ', team = ' + team + ', picture = ' + picture + ', idf = ' + idf + ', idb = ' + idb + ', signature = ' + signature + ', type = ' + type);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Team/playersManagementCreateSave.php',
		data: {name: name, lastname: lastname, lastname2: lastname2, nickname: nickname, birthdate: birthdate, playernumber: playernumber, phone: phone, sex: sex, email: email, id: id, comments: comments, valid: valid, status: status, team: team, picture: picture, idf: idf, idb: idb, signature: signature, type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataPlayerMessage);
				playersManagementTeamShow(catTeamPlayerCat, catTeamPlayerTeam);
			}else{
				alert(res.dataPlayerMessage);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function playersManagementTeamShowEdit(player){
	//console.log('playersManagementTeamShowEdit player = ' + player);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Team/playersManagementEdit.php',
		data: {player: player},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#playersManagementEdit").toggle();
				$("#playersManagementList").toggle();
				$("#playersManagementEdit").html(res.dataPlayerEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function playersManagementTeamEditPlayer(playerid, name, lastname, lastname2, nickname, birthdate, playernumber, phone, sex, email, id, comments, valid, status, team, picture, idf, idb, signature, type, steam, idpdf){
	//console.log('playersManagementTeamEditPlayer playerid = ' + playerid + ', name = ' + name + ', lastname = ' + lastname + ', lastname2 = ' + lastname2 + ', nickname = ' + nickname + ', birthdate = ' + birthdate + ', playernumber = ' + playernumber + ', phone = ' + phone + ', sex = ' + sex + ', email = ' + email + ', id = ' + id + ', comments = ' + comments + ', valid = ' + valid + ', status = ' + status + ', team = ' + team + ', picture = ' + picture + ', idf = ' + idf + ', idb = ' + idb + ', signature = ' + signature + ', type = ' + type);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Players/Team/playersManagementEditSave.php',
		data: {playerid: playerid, name: name, lastname: lastname, lastname2: lastname2, nickname: nickname, birthdate: birthdate, playernumber: playernumber, phone: phone, sex: sex, email: email, id: id, comments: comments, valid: valid, status: status, team: team, picture: picture, idf: idf, idb: idb, signature: signature, type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataPlayerMessage);
				playersManagementTeamShow(catTeamPlayerCat, catTeamPlayerTeam);
			}else{
				alert(res.dataPlayerMessage);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function readURL(input, foto) {
	var fd = new FormData();
	var files = $('#myFoto')[0].files;
	fd.append('myFoto',files[0]);
	$("#previewMyFoto").html('');
	$("#previewMyFoto").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$.ajax({
		type: 'POST',
		enctype: 'multipart/form-data',
		dataType: 'json',
		url: 'objects/UploadPicture.php',
		data: fd,
		contentType: false,
		processData: false,
		success: function (data) {
			if(data.status !== '1'){ 
				$('#foto').src(''); 
				$('#previewMyFoto').html(data.alert);
			}
			if(data.status === '1'){ 
				$('#previewMyFoto').html(data.alert); 
				$('#myFotoFileName').val(data.action);
			} 
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + foto)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + foto).show();
	}
}

function readURLE(input, fotoE) {
	var fd = new FormData();
	var files = $('#myFotoE')[0].files;
	fd.append('myFotoE',files[0]);
	$("#previewMyFotoE").html('');
	$("#previewMyFotoE").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$.ajax({
		type: 'POST',
		enctype: 'multipart/form-data',
		dataType: 'json',
		url: 'objects/UploadPictureE.php',
		data: fd,
		contentType: false,
		processData: false,
		success: function (data) {
			if(data.status !== '1'){ 
				$('#fotoE').src(''); 
				$('#previewMyFotoE').html(data.alert);
			}
			if(data.status === '1'){ 
				$('#previewMyFotoE').html(data.alert); 
				$('#myFotoFileNameE').val(data.action);
			} 
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + fotoE)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + fotoE).show();
	}
}

function readIDPDFURL(input, identificacion) {
	var fd = new FormData();
	var files = $('#myIDPDF')[0].files;
	fd.append('myIDPDF',files[0]);
	$("#previewMyIDPDF").html('');
	$("#previewMyIDPDF").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$.ajax({
		type: 'POST',
		enctype: 'multipart/form-data',
		dataType: 'json',
		url: 'objects/UploadIDPDF.php',
		data: fd,
		contentType: false,
		processData: false,
		success: function (data) {
			$('#previewMyIDPDF').html(data.alert);
			if(data.status === '1'){
				$('#myIDPDFFileName').val(data.action);
			}else{
				$('#myIDPDFFileName').val('');
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	if (input.files && input.files[0]) {
		$('#' + identificacion).attr('src', URL.createObjectURL(input.files[0]));
		$('#' + identificacion).show();
	}
}

function readIDPDFURLE(input, identificacion) {
	var fd = new FormData();
	var files = $('#myIDPDFE')[0].files;
	fd.append('myIDPDF',files[0]);
	$("#previewMyIDPDFE").html('');
	$("#previewMyIDPDFE").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$.ajax({
		type: 'POST',
		enctype: 'multipart/form-data',
		dataType: 'json',
		url: 'objects/UploadIDPDF.php',
		data: fd,
		contentType: false,
		processData: false,
		success: function (data) {
			$('#previewMyIDPDFE').html(data.alert);
			if(data.status === '1'){
				$('#myIDPDFFileNameE').val(data.action);
			}else{
				$('#myIDPDFFileNameE').val('');
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	if (input.files && input.files[0]) {
		$('#' + identificacion).attr('src', URL.createObjectURL(input.files[0]));
		$('#' + identificacion).show();
	}
}

function readIDURL11(input, identificacion) {
	var fd = new FormData();
	var files = $('#myID11')[0].files;
	fd.append('myID11',files[0]);
	$("#previewMyID11").html('');
	$("#previewMyID11").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$.ajax({
		type: 'POST',
		enctype: 'multipart/form-data',
		dataType: 'json',
		url: 'objects/UploadIDF.php',
		data: fd,
		contentType: false,
		processData: false,
		success: function (data) {		
			if(data.status !== '1'){ 
				$('#identificacion11').src(''); 
				$('#previewMyID11').html(data.alert);
			}
			if(data.status === '1'){ 
				$('#previewMyID11').html(data.alert); 
				$('#myID11FileName').val(data.action);
			} 
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + identificacion)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + identificacion).show();
	}
}

function readIDURL11E(input, identificacion) {
	var fd = new FormData();
	var files = $('#myID11E')[0].files;
	fd.append('myID11E',files[0]);
	$("#previewMyID11E").html('');
	$("#previewMyID11E").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$.ajax({
		type: 'POST',
		enctype: 'multipart/form-data',
		dataType: 'json',
		url: 'objects/UploadIDFE.php',
		data: fd,
		contentType: false,
		processData: false,
		success: function (data) {		
			if(data.status !== '1'){ 
				$('#identificacion11E').src(''); 
				$('#previewMyID11E').html(data.alert);
			}
			if(data.status === '1'){ 
				$('#previewMyID11E').html(data.alert); 
				$('#myID11FileNameE').val(data.action);
			} 
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + identificacion)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + identificacion).show();
	}
}

function readIDURL12(input, identificacion) {
	var fd = new FormData();
	var files = $('#myID12')[0].files;
	fd.append('myID12',files[0]);
	$("#previewMyID12").html('');
	$("#previewMyID12").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$.ajax({
		type: 'POST',
		enctype: 'multipart/form-data',
		dataType: 'json',
		url: 'objects/UploadIDB.php',
		data: fd,
		contentType: false,
		processData: false,
		success: function (data) {		
			if(data.status !== '1'){ 
				$('#identificacion12').src(''); 
				$('#previewMyID12').html(data.alert);
			}
			if(data.status === '1'){ 
				$('#previewMyID12').html(data.alert); 
				$('#myID12FileName').val(data.action);
			} 
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + identificacion)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + identificacion).show();
	}
}

function readIDURL12E(input, identificacion) {
	var fd = new FormData();
	var files = $('#myID12E')[0].files;
	fd.append('myID12E',files[0]);
	$("#previewMyID12E").html('');
	$("#previewMyID12E").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$.ajax({
		type: 'POST',
		enctype: 'multipart/form-data',
		dataType: 'json',
		url: 'objects/UploadIDBE.php',
		data: fd,
		contentType: false,
		processData: false,
		success: function (data) {		
			if(data.status !== '1'){ 
				$('#identificacion12E').src(''); 
				$('#previewMyID12E').html(data.alert);
			}
			if(data.status === '1'){ 
				$('#previewMyID12E').html(data.alert); 
				$('#myID12FileNameE').val(data.action);
			} 
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + identificacion)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + identificacion).show();
	}
}

function readFirmaURL(input, firma) {
	var fd = new FormData();
	var files = $('#myFirma')[0].files;
	fd.append('myFirma',files[0]);
	$("#previewMyFirma").html('');
	$("#previewMyFirma").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$.ajax({
		type: 'POST',
		enctype: 'multipart/form-data',
		dataType: 'json',
		url: 'objects/UploadSignature.php',
		data: fd,
		contentType: false,
		processData: false,
		success: function (data) {		
			if(data.status !== '1'){ 
				$('#firma').src(''); 
				$('#previewMyFirma').html(data.alert);
			}
			if(data.status === '1'){ 
				$('#previewMyFirma').html(data.alert); 
				$('#myFirmaFileName').val(data.action);
			} 
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + firma)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + firma).show();
	}
}

function readFirmaURLE(input, firma) {
	var fd = new FormData();
	var files = $('#myFirmaE')[0].files;
	fd.append('myFirmaE',files[0]);
	$("#previewMyFirmaE").html('');
	$("#previewMyFirmaE").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$.ajax({
		type: 'POST',
		enctype: 'multipart/form-data',
		dataType: 'json',
		url: 'objects/UploadSignatureE.php',
		data: fd,
		contentType: false,
		processData: false,
		success: function (data) {		
			if(data.status !== '1'){ 
				$('#firmaE').src(''); 
				$('#previewMyFirmaE').html(data.alert);
			}
			if(data.status === '1'){ 
				$('#previewMyFirmaE').html(data.alert); 
				$('#myFirmaFileNameE').val(data.action);
			} 
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + firma)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + firma).show();
	}
}
/*****************************************************************************************************************
*****************************************Player Management Team***************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
************************************************Config Admin******************************************************
*****************************************************************************************************************/

function configManagementShow(){
	//console.log('configManagementShow Team = ' + Team);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Config/configManagementShow.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataConfig);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function readURLLogo(input, logoImage) {
	var fd = new FormData();
	var files = $('#myLogo')[0].files;
	fd.append('myLogo',files[0]);
	$("#previewMyLogo").html('');
	$("#previewMyLogo").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$.ajax({
		type: 'POST',
		enctype: 'multipart/form-data',
		dataType: 'json',
		url: 'objects/UploadLogo.php',
		data: fd,
		contentType: false,
		processData: false,
		success: function (data) {
			console.log(data.status);
			if(data.status !== '1'){ 
				$('#foto').src(''); 
				$('#previewMyLogo').html(data.alert);
			}
			if(data.status === '1'){ 
				$('#previewMyLogo').html(data.alert); 
				$('#myLogoFileName').val(data.action);
			} 
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + logoImage)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + logoImage).show();
	}
}

function configManagementInfoSave(leagueName, latitude, longitude, logo, logox, logoy, logoFileName, colorHeader, colorBody, colorFooter){
	//console.log('configManagementInfoSave leagueName = ' + leagueName + ', latitude = ' + latitude + ', longitude = ' + longitude + ', logo = ' + logo + ', logox = ' + logox + ', logoy = ' + logoy + ', logoFileName = ' + logoFileName + ', colorHeader = ' + colorHeader + ', colorBody = ' + colorBody + ', colorFooter = ' + colorFooter);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Config/configManagementInfoSave.php',
		data: {leagueName: leagueName, latitude: latitude, longitude: longitude, logo: logo, logox: logox, logoy: logoy, logoFileName: logoFileName, colorHeader: colorHeader, colorBody: colorBody, colorFooter: colorFooter},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataConfigAnswer);
			}else{
				alert(res.dataConfigAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function configManagementAlertSave(Alert){
	//console.log('configManagementAlertSave Alert = ' + Alert);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Config/configManagementAlertSave.php',
		data: {Alert: Alert},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataConfigAnswer);
			}else{
				alert(res.dataConfigAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function configManagementGeneralSave(lenguaje, EmpatesPenales, JugadorJugado, JuegoCedulas, MarcadorArbitro, MarcadorFecha, MarcadorDiaDefault, JornadaCedulas, columnid, ByeWeekPoints, ByeWeekPointsGoals, juegoSemanal, tressets, 
                                perfilJugador, jugadoresApellidos1, juegosxnombre, coachjuegos, coachjuegosdiainicial, coachjuegosdiafinal, hora, hora2, tarjetaCambios, VBByeWeekSets, VBByeWeekPoints, VBByeWeekSetPoints, playerIDPDF, playerSignature){
	//console.log('configManagementAlertSave');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Config/configManagementGeneralSave.php',
		data: {EmpatesPenales: EmpatesPenales, JugadorJugado: JugadorJugado, JuegoCedulas: JuegoCedulas, MarcadorArbitro: MarcadorArbitro, MarcadorFecha: MarcadorFecha, 
		JornadaCedulas: JornadaCedulas, columnid: columnid, MarcadorDiaDefault: MarcadorDiaDefault, lenguaje: lenguaje, ByeWeekPoints: ByeWeekPoints, ByeWeekPointsGoals: ByeWeekPointsGoals, 
		juegoSemanal: juegoSemanal, tressets: tressets, perfilJugador:perfilJugador,jugadoresApellidos1: jugadoresApellidos1,juegosXNombre: juegosxnombre, coachjuegos: coachjuegos, coachjuegosdiainicial: coachjuegosdiainicial, 
		coachjuegosdiafinal: coachjuegosdiafinal, hora: hora, hora2: hora2, tarjetaCambios: tarjetaCambios, VBByeWeekSets: VBByeWeekSets, VBByeWeekPoints: VBByeWeekPoints, VBByeWeekSetPoints: VBByeWeekSetPoints, playerIDPDF: playerIDPDF, playerSignature: playerSignature},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataConfigAnswer);
			}else{
				alert(res.dataConfigAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

/*****************************************************************************************************************
************************************************Config Admin******************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
************************************************Colours Admin*****************************************************
*****************************************************************************************************************/

function colorManagementShow(){
	//console.log('colorManagementShow');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Colors/colorsManagement.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataColor);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function colorsManagementCreateSave(colorName, colorHEX){
	//console.log('colorsManagementCreateSave colorName = ' + colorName + ', colorHEX = ' + colorHEX');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Colors/colorsManagementNewSave.php',
		data: {colorName: colorName, colorHEX: colorHEX},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataColorAnswer);
				colorManagementShow();
			}else{
				alert(res.dataColorAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function colorsManagementShowEdit(color){
	//console.log('colorsManagementShowEdit color = ' + color);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Colors/colorsManagementEdit.php',
		data: {color: color},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#colorsManagementEdit").css('display', 'block');
				$("#colorsManagementList").css('display', 'none');
				$("#colorsManagementEdit").html(res.dataColorEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function colorsManagementShowAdd(){
	//console.log('colorsManagementShowAdd color = ' + color);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Colors/colorsManagementCreate.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#colorsManagementCreate").css('display', 'block');
				$("#colorsManagementList").css('display', 'none');
				$("#colorsManagementCreate").html(res.dataColorAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function colorsManagementUpdateSave(colorID, colorName, colorHEX){
	//console.log('colorsManagementUpdateSave colorName = ' + colorName + ', colorHEX = ' + colorHEX');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Colors/colorsManagementUpdateSave.php',
		data: {colorName: colorName, colorHEX: colorHEX, colorID: colorID},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataColorAnswer);
				colorManagementShow();
			}else{
				alert(res.dataColorAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

/*****************************************************************************************************************
************************************************Colours Admin*****************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
**********************************************Tournament Admin****************************************************
*****************************************************************************************************************/

function tournamentManagementShow(){
	//console.log('tournamentManagementShow');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Tournament/TournamentsManagement.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataTournament);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function tournamentsManagementShowAdd(){
	//console.log('tournamentsManagementShowAdd');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Tournament/TournamentsManagementCreate.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#tournamentsManagementCreate").css('display', 'block');
				$("#tournamentsManagementList").css('display', 'none');
				$("#tournamentsManagementCreate").html(res.tournamentAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function tournamentsManagementHideAdd(){
	//console.log('tournamentsManagementHideAdd');
	$("#tournamentsManagementCreate").css('display', 'none');
	$("#tournamentsManagementList").css('display', 'block');
	$("#tournamentsManagementCreate").html('');
}

function tournamentsManagementCreateSave(tournamentName, tournamentActual, tournamentInscr, tournamentVs, tournamentWeeks){
	//console.log('tournamentsManagementCreateSave tournamentName = ' + tournamentName + ', tournamentActual = ' + tournamentActual + ', tournamentInscr = ' + tournamentInscr + ', tournamentVs = ' + tournamentVs + ', tournamentWeeks = ' + tournamentWeeks);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Tournament/TournamentsManagementNewSave.php',
		data: {tournamentName: tournamentName, tournamentActual: tournamentActual, tournamentInscr: tournamentInscr, tournamentVs: tournamentVs, tournamentWeeks: tournamentWeeks},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataTournamentAnswer);
				tournamentManagementShow();
			}else{
				alert(res.dataTournamentAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function tournamentsManagementShowEdit(tournament){
	//console.log('tournamentsManagementShowEdit tournament = ' + tournament);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Tournament/TournamentsManagementEdit.php',
		data: {tournament: tournament},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#tournamentsManagementEdit").css('display', 'block');
				$("#tournamentsManagementList").css('display', 'none');
				$("#tournamentsManagementEdit").html(res.dataTournamentEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function tournamentsManagementHideEdit(){
	//console.log('tournamentsManagementHideEdit');
	$("#tournamentsManagementEdit").css('display', 'none');
	$("#tournamentsManagementList").css('display', 'block');
	$("#tournamentsManagementEdit").html('');
}

function tournamentsManagementEditSave(tournamentid, tournamentName, tournamentActual, tournamentInscr, tournamentVs, tournamentWeeks){
	//console.log('tournamentsManagementEditSave tournamentid = ' + tournamentid + ', tournamentName = ' + tournamentName + ', tournamentActual = ' + tournamentActual + ', tournamentInscr = ' + tournamentInscr + ', tournamentVs = ' + tournamentVs + ', tournamentWeeks = ' + tournamentWeeks);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Tournament/TournamentsManagementUpdateSave.php',
		data: {tournamentid: tournamentid, tournamentName: tournamentName, tournamentActual: tournamentActual, tournamentInscr: tournamentInscr, tournamentVs: tournamentVs, tournamentWeeks: tournamentWeeks},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataTournamentAnswer);
				tournamentManagementShow();
			}else{
				alert(res.dataTournamentAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

/*****************************************************************************************************************
**********************************************Tournament Admin****************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
***********************************************Category Admin*****************************************************
*****************************************************************************************************************/

function categoryManagementShow(){
	//console.log('categoryManagementShow');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Category/CategoriesManagement.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataCategory);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function categoryManagementShowAdd(){
	//console.log('tournamentsManagementShowAdd');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Category/CategoriesManagementCreate.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#categoriesManagementCreate").css('display', 'block');
				$("#categoriesManagementList").css('display', 'none');
				$("#categoriesManagementCreate").html(res.categoryAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function categoryManagementHideAdd(){
	//console.log('categoryManagementHideAdd');
	$("#categoriesManagementCreate").css('display', 'none');
	$("#categoriesManagementList").css('display', 'block');
	$("#categoriesManagementCreate").html('');
}

function categoryManagementCreateSave(descripcion, orden, Inicial, Final, Color, Calendario, Rondas){
	//console.log('categoryManagementCreateSave descripcion = ' + descripcion + ', orden = ' + orden + ', Inicial = ' + Inicial + ', Final = ' + Final + ', Color = ' + Color + ', Calendario = ' + Calendario + ', Rondas = ' + Rondas);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Category/CategoriesManagementNewSave.php',
		data: {descripcion: descripcion, orden: orden, Inicial: Inicial, Final: Final, Color: Color, Calendario: Calendario, Rondas: Rondas},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataCategoryAnswer);
				categoryManagementShow();
			}else{
				alert(res.dataCategoryAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function categoryManagementShowEdit(id){
	//console.log('categoryManagementShowEdit category_id = ' + id);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Category/CategoriesManagementEdit.php',
		data: {category: id},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#categoriesManagementEdit").css('display', 'block');
				$("#categoriesManagementList").css('display', 'none');
				$("#categoriesManagementEdit").html(res.dataCategoryEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function categoryManagementHideEdit(){
	//console.log('categoryManagementHideEdit');
	$("#categoriesManagementEdit").css('display', 'none');
	$("#categoriesManagementList").css('display', 'block');
	$("#categoriesManagementEdit").html('');
}

function categoryManagementEditSave(id, descripcion, orden, Inicial, Final, Color, Calendario, Rondas){
	//console.log('categoryManagementEditSave id = ' + id + ', descripcion = ' + descripcion + ', orden = ' + orden + ', Inicial = ' + Inicial + ', Final = ' + Final + ', Color = ' + Color + ', Calendario = ' + Calendario + ', Rondas = ' + Rondas);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Category/CategoriesManagementUpdateSave.php',
		data: {id: id, descripcion: descripcion, orden: orden, Inicial: Inicial, Final: Final, Color: Color, Calendario: Calendario, Rondas: Rondas},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataCategoryAnswer);
				categoryManagementShow();
			}else{
				alert(res.dataCategoryAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

/*****************************************************************************************************************
***********************************************Category Admin*****************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
***********************************************Calendar Admin*****************************************************
*****************************************************************************************************************/

function calendarManagementShow(){
	//console.log('calendarManagementShow');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Calendar/CalendarsManagement.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataCalendar);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function calendarManagementShowAdd(){
	//console.log('calendarManagementShowAdd');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Calendar/CalendarsManagementCreate.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#calendarsManagementCreate").css('display', 'block');
				$("#calendarsManagementList").css('display', 'none');
				$("#calendarsManagementCreate").html(res.calendarAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function calendarManagementHideAdd(){
	//console.log('calendarManagementHideAdd');
	$("#calendarsManagementCreate").css('display', 'none');
	$("#calendarsManagementList").css('display', 'block');
	$("#calendarsManagementCreate").html('');
}

function calendarManagementCreateSave(descripcion){
	//console.log('calendarManagementCreateSave descripcion = ' + descripcion);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Calendar/CalendarsManagementNewSave.php',
		data: {descripcion: descripcion},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataCalendarAnswer);
				calendarManagementShow();
			}else{
				alert(res.dataCalendarAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function calendarManagementShowEdit(id){
	//console.log('calendarManagementShowEdit category_id = ' + id);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Calendar/CalendarsManagementEdit.php',
		data: {calendar: id},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#calendarsManagementEdit").css('display', 'block');
				$("#calendarsManagementList").css('display', 'none');
				$("#calendarsManagementEdit").html(res.dataCalendarEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function calendarManagementHideEdit(){
	//console.log('calendarManagementHideEdit');
	$("#calendarsManagementEdit").css('display', 'none');
	$("#calendarsManagementList").css('display', 'block');
	$("#calendarsManagementEdit").html('');
}

function calendarManagementEditSave(id, descripcion){
	//console.log('calendarManagementEditSave id = ' + id + ', descripcion = ' + descripcion);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Calendar/CalendarsManagementUpdateSave.php',
		data: {id: id, descripcion: descripcion},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataCalendarAnswer);
				calendarManagementShow();
			}else{
				alert(res.dataCategoryAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

/*****************************************************************************************************************
***********************************************Calendar Admin*****************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
***********************************************Field Admin*****************************************************
*****************************************************************************************************************/

function fieldManagementShow(){
	//console.log('fieldManagementShow');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Fields/FieldsManagement.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataField);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function fieldManagementShowAdd(){
	//console.log('fieldManagementShowAdd');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Fields/FieldsManagementCreate.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#fieldsManagementCreate").css('display', 'block');
				$("#fieldsManagementList").css('display', 'none');
				$("#fieldsManagementCreate").html(res.fieldAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function fieldManagementHideAdd(){
	//console.log('fieldManagementHideAdd');
	$("#fieldsManagementCreate").css('display', 'none');
	$("#fieldsManagementList").css('display', 'block');
	$("#fieldsManagementCreate").html('');
}

function fieldManagementCreateSave(descripcion, lat, long, zoom, google){
	//console.log('fieldManagementCreateSave descripcion = ' + descripcion + ', lat = ' + lat + ', long = ' + long + ', zoom = ' + zoom + ', google = ' + google);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Fields/FieldsManagementNewSave.php',
		data: {descripcion: descripcion, lat: lat, long: long, zoom: zoom, google: google},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataFieldAnswer);
				fieldManagementShow();
			}else{
				alert(res.dataFieldAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function fieldManagementShowEdit(id){
	//console.log('fieldManagementShowEdit field_id = ' + id);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Fields/FieldsManagementEdit.php',
		data: {field: id},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#fieldsManagementEdit").css('display', 'block');
				$("#fieldsManagementList").css('display', 'none');
				$("#fieldsManagementEdit").html(res.dataFieldEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function fieldManagementHideEdit(){
	//console.log('fieldManagementHideEdit');
	$("#fieldsManagementEdit").css('display', 'none');
	$("#fieldsManagementList").css('display', 'block');
	$("#fieldsManagementEdit").html('');
}

function fieldManagementEditSave(id, descripcion, lat, long, zoom, google){
	//console.log('fieldManagementEditSave id = ' + id + ', descripcion = ' + descripcion + ', lat = ' + lat + ', long = ' + long + ', zoom = ' + zoom + ', google = ' + google);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Fields/FieldsManagementUpdateSave.php',
		data: {id: id, descripcion: descripcion, lat: lat, long: long, zoom: zoom, google: google},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataFieldAnswer);
				fieldManagementShow();
			}else{
				alert(res.dataFieldAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

/*****************************************************************************************************************
***********************************************Field Admin*****************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
***********************************************Team Admin*****************************************************
*****************************************************************************************************************/
var teamsManagementFilterTimer = null;

function teamsManagementFilterListDebounced() {
	if (teamsManagementFilterTimer) {
		clearTimeout(teamsManagementFilterTimer);
	}
	teamsManagementFilterTimer = setTimeout(function () {
		teamsManagementReloadList();
	}, 350);
}

function teamsManagementReloadList() {
	var filterVal = '';
	var $input = $('#teamListFilter');
	if ($input.length) {
		filterVal = $input.val();
	}
	var category = $('#playersManagementAdminSelectedCategory').val();
	if (!category) {
		return;
	}
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Teams/TeamsManagementReloadList.php',
		data: { Category: category, teamListFilter: filterVal },
		success: function (res) {
			if (res.status === '1') {
				var $wrap = $('#teamsManagementListTables');
				if ($wrap.length) {
					$wrap.replaceWith(res.dataTeamList);
				}
			}
		},
		error: function (jqxhr, status, exception) {
			console.log('Exception:' + exception);
		}
	});
}

function teamsManagementAdminCategoryShow(){
	//console.log('teamsManagementAdminCategoryShow');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Teams/TeamsManagementChangeCategories.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataCategory);
				teamsManagementAdminCategoryTeamShow(res.category);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function teamsManagementAdminCategoryTeamShow(Category){
	//console.log('teamsManagementAdminCategoryTeamShow Category = ' + Category);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Teams/TeamsManagement.php',
		data: {Category: Category},
		success: function (res) {
			mainLoadingOff();
			if (res.status === '1') {
				$('#teamContent').html(res.dataTeam);
				teamsManagementAdminCategoryTeamShowReloadList(Category);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function teamsManagementAdminCategoryTeamShowReloadList(Category){
    //console.log('teamsManagementAdminCategoryTeamShowReloadList Category = ' + Category);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Teams/TeamsManagementChangeCategoriesReloadList.php',
		data: {Category: Category},
		success: function (res) {
			mainLoadingOff();
			$("#teamContentCategoryList").html(res.dataCategories);
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function teamManagementShowAdd(Category){
	//console.log('teamManagementShowAdd');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Teams/TeamsManagementCreate.php',
		data: {Category: Category},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#teamsManagementCreate").css('display', 'block');
				$("#teamsManagementList").css('display', 'none');
				$("#teamsManagementCreate").html(res.teamAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function teamManagementHideAdd(){
	//console.log('teamManagementHideAdd');
	$("#teamsManagementCreate").css('display', 'none');
	$("#teamsManagementList").css('display', 'block');
	$("#teamsManagementCreate").html('');
}

function teamManagementCreateSave(categoria, descripcion, descripcionlarga, estatus, fuerza, institucion, campo, playera, short, calcetas, file, desc3, nombreColor, credencialColor){
	//console.log('teamManagementCreateSave descripcion = ' + descripcion + ', descripcionLarga = ' + descripcionLarga + ', estatus = ' + estatus + ', fuerza = ' + fuerza + ', campo = ' + campo);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Teams/TeamsManagementNewSave.php',
		data: {descripcion: descripcion, descripcionlarga: descripcionlarga, estatus: estatus, fuerza: fuerza, institucion: institucion, campo: campo, playera: playera, short: short, calcetas: calcetas, file: file, desc3: desc3, nombreColor: nombreColor, credencialColor: credencialColor},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataTeamdAnswer);
				teamsManagementAdminCategoryTeamShow(categoria);
			}else{
				alert(res.dataTeamdAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function teamManagementShowEdit(id, category){
	//console.log('teamManagementShowEdit team = ' + id);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Teams/TeamsManagementEdit.php',
		data: {team: id, category: category},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#teamsManagementEdit").css('display', 'block');
				$("#teamsManagementList").css('display', 'none');
				$("#teamsManagementEdit").html(res.dataTeamEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function teamManagementHideEdit(){
	//console.log('teamManagementHideEdit');
	$("#teamsManagementEdit").css('display', 'none');
	$("#teamsManagementList").css('display', 'block');
	$("#teamsManagementEdit").html('');
}

function teamManagementEditSave(id, categoria, descripcion, descripcionlarga, estatus, fuerza, institucion, campo, playera, short, calcetas, file, desc3, nombreColor, credencialColor){
	//console.log('teamManagementEditSave id = ' + id + ', descripcion = ' + descripcion + ', descripcionLarga = ' + descripcionLarga + ', estatus = ' + estatus + ', fuerza = ' + fuerza + ', campo = ' + campo);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Teams/TeamsManagementUpdateSave.php',
		data: {id: id, descripcion: descripcion, descripcionlarga: descripcionlarga, estatus: estatus, fuerza: fuerza, institucion: institucion, campo: campo, playera: playera, short: short, calcetas: calcetas, file: file, desc3: desc3, nombreColor: nombreColor, credencialColor: credencialColor},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataTeamdAnswer);
				teamsManagementAdminCategoryTeamShow(categoria);
			}else{
				alert(res.dataTeamdAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

/*****************************************************************************************************************
***********************************************Institution Admin**************************************************
*****************************************************************************************************************/
var institutionsManagementFilterTimer = null;

function institutionsManagementFilterListDebounced() {
	if (institutionsManagementFilterTimer) {
		clearTimeout(institutionsManagementFilterTimer);
	}
	institutionsManagementFilterTimer = setTimeout(function () {
		institutionsManagementReloadList();
	}, 350);
}

function institutionsManagementReloadList() {
	var filterVal = '';
	var $input = $('#institutionListFilter');
	if ($input.length) {
		filterVal = $input.val();
	}
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Institutions/InstitutionsManagementReloadList.php',
		data: { institutionListFilter: filterVal },
		success: function (res) {
			if (res.status === '1') {
				var $wrap = $('#institutionsManagementListTables');
				if ($wrap.length) {
					$wrap.replaceWith(res.dataInstitutionList);
				}
			}
		},
		error: function (jqxhr, status, exception) {
			console.log('Exception:' + exception);
		}
	});
}

function institutionsManagementShow(){
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Institutions/InstitutionsManagement.php',
		success: function (res) {
			mainLoadingOff();
			if (res.status === '1') {
				$("#body").html(res.dataInstitution);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function institutionManagementShowAdd(){
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Institutions/InstitutionsManagementCreate.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#institutionsManagementCreate").css('display', 'block');
				$("#institutionsManagementList").css('display', 'none');
				$("#institutionsManagementCreate").html(res.institutionAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function institutionManagementHideAdd(){
	$("#institutionsManagementCreate").css('display', 'none');
	$("#institutionsManagementList").css('display', 'block');
	$("#institutionsManagementCreate").html('');
}

function institutionManagementCreateSave(descripcion, descripcionlarga, estatus, file, desc5){
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Institutions/InstitutionsManagementNewSave.php',
		data: {descripcion: descripcion, descripcionlarga: descripcionlarga, estatus: estatus, file: file, desc5: desc5},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataInstitutionAnswer);
				institutionsManagementShow();
			}else{
				alert(res.dataInstitutionAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function institutionManagementShowEdit(id){
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Institutions/InstitutionsManagementEdit.php',
		data: {institution: id},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#institutionsManagementEdit").css('display', 'block');
				$("#institutionsManagementList").css('display', 'none');
				$("#institutionsManagementEdit").html(res.dataInstitutionEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function institutionManagementHideEdit(){
	$("#institutionsManagementEdit").css('display', 'none');
	$("#institutionsManagementList").css('display', 'block');
	$("#institutionsManagementEdit").html('');
}

function institutionManagementEditSave(id, descripcion, descripcionlarga, estatus, file, desc5){
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Institutions/InstitutionsManagementUpdateSave.php',
		data: {id: id, descripcion: descripcion, descripcionlarga: descripcionlarga, estatus: estatus, file: file, desc5: desc5},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataInstitutionAnswer);
				institutionsManagementShow();
			}else{
				alert(res.dataInstitutionAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function readTeamLogoURL(input, Logo) {
	var fd = new FormData();
	var files = $('#myLogo')[0].files;
	fd.append('myLogo',files[0]);
	$("#previewMyLogo").html('');
	$("#previewMyLogo").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$.ajax({
		type: 'POST',
		enctype: 'multipart/form-data',
		dataType: 'json',
		url: 'objects/UploadTeamLogo.php',
		data: fd,
		contentType: false,
		processData: false,
		success: function (data) {
			if(data.status !== '1'){ 
				$('#Logo').src(''); 
				$('#previewMyLogo').html(data.alert);
			}
			if(data.status === '1'){ 
				$('#previewMyLogo').html(data.alert); 
				$('#myLogoFileName').val(data.action);
			} 
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + Logo)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + Logo).show();
	}
}
/*****************************************************************************************************************
*************************************************Team Admin*******************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
*************************************************Weeks Admin******************************************************
*****************************************************************************************************************/

function weekManagementShow(){
	//console.log('weekManagementShow');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Week/WeeksManagementChangeCalendar.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataCalendar);
				weeksManagementAdminCalendarWeeksShow(res.calendar);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function weeksManagementAdminCalendarWeeksShow(Calendar){
	//console.log('weeksManagementAdminCalendarWeeksShow Calendar = ' + Calendar);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Week/WeeksManagement.php',
		data: {Calendar: Calendar},
		success: function (res) {
			mainLoadingOff();
			if (res.status === '1') {
				$('#weekContent').html(res.dataWeek);
				weeksManagementAdminCalendarWeekShowReloadList(Calendar);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function weeksManagementAdminCalendarWeekShowReloadList(Calendar){
    //console.log('weeksManagementAdminCalendarWeekShowCalendarReloadList Calendar = ' + Calendar);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Week/WeeksManagementChangeCalendarReloadList.php',
		data: {Calendar: Calendar},
		success: function (res) {
			mainLoadingOff();
			$("#weekContentCalendarList").html(res.dataCalendar);
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function weekManagementShowAdd(Calendar){
	//console.log('weekManagementShowAdd');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Week/WeeksManagementCreate.php',
		data: {Calendar: Calendar},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#weeksManagementCreate").css('display', 'block');
				$("#weeksManagementList").css('display', 'none');
				$("#weeksManagementCreate").html(res.weekAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function weekManagementHideAdd(){
	//console.log('weekManagementHideAdd');
	$("#weeksManagementCreate").css('display', 'none');
	$("#weeksManagementList").css('display', 'block');
	$("#weeksManagementCreate").html('');
}

function weekManagementCreateSave(Desc, DescCorta, Orden, Fecha, Inicio, Fin, Calendar, Type){
	//console.log('weekManagementCreateSave Desc = ' + Desc + ', DescCorta = ' + DescCorta + ', Orden = ' + Orden + ', Fecha = ' + Fecha + ', Inicio = ' + Inicio + ', Fin = ' + Fin + ', Calendar = ' + Calendar + ', Type = ' + Type);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Week/WeeksManagementNewSave.php',
		data: {Desc: Desc, DescCorta: DescCorta, Orden: Orden, Fecha: Fecha, Inicio: Inicio, Fin: Fin, Calendar: Calendar, Type: Type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataWeekAnswer);
				weeksManagementAdminCalendarWeeksShow(Calendar);
			}else{
				alert(res.dataWeekAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function weekManagementShowEdit(id, Calendar){
	//console.log('weekManagementShowEdit week_id = ' + id);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Week/WeeksManagementEdit.php',
		data: {week: id, Calendar: Calendar},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#weeksManagementEdit").css('display', 'block');
				$("#weeksManagementList").css('display', 'none');
				$("#weeksManagementEdit").html(res.dataWeekEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function weekManagementHideEdit(){
	//console.log('weekManagementHideEdit');
	$("#weeksManagementEdit").css('display', 'none');
	$("#weeksManagementList").css('display', 'block');
	$("#weeksManagementEdit").html('');
}

function weekManagementEditSave(id, Desc, DescCorta, Orden, Fecha, Inicio, Fin, Calendar, Type){
	//console.log('weekManagementEditSave id = ' + id + ', Desc = ' + Desc + ', DescCorta = ' + DescCorta + ', Orden = ' + Orden + ', Fecha = ' + Fecha + ', Inicio = ' + Inicio + ', Fin = ' + Fin + ', Type = ' + Type);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Week/WeeksManagementUpdateSave.php',
		data: {id: id, Desc: Desc, DescCorta: DescCorta, Orden: Orden, Fecha: Fecha, Inicio: Inicio, Fin: Fin, Type: Type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataWeekAnswer);
				weeksManagementAdminCalendarWeeksShow(Calendar);
			}else{
				alert(res.dataWeekAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

/*****************************************************************************************************************
*************************************************Weeks Admin******************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
************************************************Alerts Admin******************************************************
*****************************************************************************************************************/

function alertManagementShow(){
	//console.log('alertManagementShow');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Alert/AlertsManagement.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataAlert);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function alertManagementShowAdd(){
	//console.log('alertManagementShowAdd');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Alert/AlertsManagementCreate.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#alertsManagementCreate").css('display', 'block');
				$("#alertsManagementList").css('display', 'none');
				$("#alertsManagementCreate").html(res.alertAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function alertManagementHideAdd(){
	//console.log('alertManagementHideAdd');
	$("#alertsManagementCreate").css('display', 'none');
	$("#alertsManagementList").css('display', 'block');
	$("#alertsManagementCreate").html('');
}

function alertManagementCreateSave(Titulo, Inicio, Fin, Status, editor, mostrar){
	//console.log('alertManagementCreateSave Titulo = ' + Titulo + ', Inicio = ' + Inicio + ', Fin = ' + Fin + ', Status = ' + Status + ', editor = ' + editor);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Alert/AlertsManagementNewSave.php',
		data: {Titulo: Titulo, Inicio: Inicio, Fin: Fin, Status: Status, editor: editor, mostrar: mostrar},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataAlertAnswer);
				alertManagementShow();
			}else{
				alert(res.dataAlertAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function alertManagementShowEdit(id){
	//console.log('alertManagementShowEdit week_id = ' + id);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Alert/AlertsManagementEdit.php',
		data: {alert: id},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#alertsManagementEdit").css('display', 'block');
				$("#alertsManagementList").css('display', 'none');
				$("#alertsManagementEdit").html(res.dataAlertEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function alertManagementHideEdit(){
	//console.log('alertManagementHideEdit');
	$("#alertsManagementEdit").css('display', 'none');
	$("#alertsManagementList").css('display', 'block');
	$("#alertsManagementEdit").html('');
}

function alertManagementEditSave(id, Titulo, Inicio, Fin, Status, editor, mostrar){
	//console.log('alertManagementEditSave id = ' + id + ', Titulo = ' + Titulo + ', Inicio = ' + Inicio + ', Fin = ' + Fin + ', Status = ' + Status + ', editor = ' + editor);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Alert/AlertsManagementUpdateSave.php',
		data: {id: id, Titulo: Titulo, Inicio: Inicio, Fin: Fin, Status: Status, editor: editor, mostrar: mostrar},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataAlertAnswer);
				alertManagementShow();
			}else{
				alert(res.dataAlertAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

/*****************************************************************************************************************
************************************************Alerts Admin******************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
*************************************************Memos Admin******************************************************
*****************************************************************************************************************/

function memoManagementShow(){
	//console.log('memoManagementShow');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Memo/MemosManagement.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataMemo);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

function memoManagementShowAdd(){
	//console.log('memoManagementShowAdd');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Memo/MemosManagementCreate.php',
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#memosManagementCreate").css('display', 'block');
				$("#memosManagementList").css('display', 'none');
				$("#memosManagementCreate").html(res.memoAdd);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function memoManagementHideAdd(){
	//console.log('alertManagementHideAdd');
	$("#memosManagementCreate").css('display', 'none');
	$("#memosManagementList").css('display', 'block');
	$("#memosManagementCreate").html('');
}

function memoManagementCreateSave(Titulo, Fecha, editor, file){
	//console.log('memoManagementCreateSave Titulo = ' + Titulo + ', Fecha = ' + Fecha + ', editor = ' + editor + ', minuta = ' + minuta);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Memo/MemosManagementNewSave.php',
		data: {Titulo: Titulo, Fecha: Fecha, editor: editor, file: file},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataMemoAnswer);
				memoManagementShow();
			}else{
				alert(res.dataMemoAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function memoManagementShowEdit(id){
	//console.log('memoManagementShowEdit week_id = ' + id);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Memo/MemosManagementEdit.php',
		data: {memo: id},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#memosManagementEdit").css('display', 'block');
				$("#memosManagementList").css('display', 'none');
				$("#memosManagementEdit").html(res.dataMemoEdit);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function memoManagementHideEdit(){
	//console.log('memoManagementHideEdit');
	$("#memosManagementEdit").css('display', 'none');
	$("#memosManagementList").css('display', 'block');
	$("#memosManagementEdit").html('');
}

function memoManagementEditSave(id, Titulo, Fecha, editor, file){
	//console.log('alertManagementEditSave id = ' + id + ', Titulo = ' + Titulo + ', Fecha = ' + Fecha + ', editor = ' + editor + ', minuta = ' + minuta);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Memo/MemosManagementUpdateSave.php',
		data: {id: id, Titulo: Titulo, Fecha: Fecha, editor: editor, file: file},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				alert(res.dataMemoAnswer);
				memoManagementShow();
			}else{
				alert(res.dataMemoAnswer);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});								 
}

/*****************************************************************************************************************
*************************************************Memos Admin******************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
*************************************************Games Admin******************************************************
*****************************************************************************************************************/

function loadWeeksAdmin(type){
    //console.log('loadWeeksAdmin');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Games/changeWeeksAdmin.php',
		data: {Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataWeeksAdmin);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdmin(Week, team, type){
    //console.log('loadWeekAdmin week = ' + Week);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Games/changeWeekAdmin.php',
		data: {Week: Week, Team:team, Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				if(type === 0){
				    loadWeekAdmminReloadList(Week, team, type);
				}
				if(type === 1){
				    loadWeekAdmminReloadListTeam(Week, team, type);
				}
				$("#weekAdminContent").html(res.dataWeekAdmin);
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdmminReloadList(Week, team, type){
    //console.log('loadWeekReloadList week = ' + Week);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Games/changeWeeksAdminReloadWeekSelector.php',
		data: {Week: Week, Team:team, Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#weekadminselectorsection").html(res.dataWeeks);
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdmminReloadListTeam(Week, team, type){
    //console.log('loadWeekReloadListTeam week = ' + Week, team = ' + team, type = ' + type);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Games/changeWeeksAdminReloadWeekTeamSelector.php',
		data: {Week: Week, Team:team, Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#teamadminselectorsection").html(res.dataWeeks);
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdminGameComments(Comment, GameID){
    //console.log('loadWeekAdminGameComments');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Games/weekAdmin-ScheduleScores-Comment.php',
		data: {GameID: GameID, Comment: Comment},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#comentarioInput").html(res.dataWeekAdminGameComment);
				$("#comentarioInput").toggle();
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function abrirFichaEdit(id, week, game, gamedesc, lgoals, vgoals, Arbitro, Comentarios, Extral, Extrav, SQL){
	//console.log('abrirFichaEdit');
	var attr = $('#edit'+id).attr('style');
	$('.juego').css('display', 'none');
	$('#expandir'+id).attr('src', './imagenes/expandir.png');	
	$('.expandirButton').attr('src', './imagenes/expandir.png');
	// For some browsers, `attr` is undefined; for others,
	// `attr` is false.  Check for both.
	if (typeof attr == typeof undefined) {
		$('#edit'+id).css('display', 'none');					
		$('#expandir'+id).attr('src', './imagenes/expandir.png');
		$("#content" + id).html('');

	}else{
		$('#edit'+id).removeAttr('style');	
		$('#expandir'+id).attr('src', './imagenes/colapsar.png');
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Admin/Games/weekAdmin-ScheduleScoresGameDetail.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals, Arbitro: Arbitro, Comentarios: Comentarios, Extral: Extral, Extrav: Extrav, SQL: SQL},
			success: function (res) {
				mainLoadingOff()
				if (res.status === '1') {
					setWeekGameDetailHtml("#content" + id, res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
				console.log('Exception:' + exception);
			}
		});
	}
}

function abrirFichaEditS(id, week, game, gamedesc, lgoals, vgoals, Arbitro, Comentarios, Extral, Extrav, SQL){
	//console.log('abrirFichaEditS');
	var attr = $('#editS'+id).attr('style');
	$('.juegoS').css('display', 'none');
	$('#expandirS'+id+'SA').attr('src', './imagenes/expandir.png');	
	$('.expandirButtonS').attr('src', './imagenes/expandir.png');
	// For some browsers, `attr` is undefined; for others,
	// `attr` is false.  Check for both.
	if (typeof attr == typeof undefined) {
		$('#editS'+id).css('display', 'none');					
		$('#expandirS'+id).attr('src', './imagenes/expandir.png');
		$("#contentS" + id).html('');

	}else{
		$('#editS'+id).removeAttr('style');	
		$('#expandirS'+id+'SA').attr('src', './imagenes/colapsar.png');
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Admin/Games/weekAdmin-ScheduleScoresGameDetailS.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals, Arbitro: Arbitro, Comentarios: Comentarios, Extral: Extral, Extrav: Extrav, SQL: SQL},
			success: function (res) {
				mainLoadingOff()
				if (res.status === '1') {
					setWeekGameDetailHtml("#contentS" + id, res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
				console.log('Exception:' + exception);
			}
		});
	}
}

function loadWeekAdminGameDetailRoja(redComment, playerID, redDays, redFee, redPaid, type){
    //console.log('loadWeekAdminGameDetailRoja');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Games/weekAdmin-ScheduleScores-RedComment.php',
		data: {RedComment: redComment, PlayerID: playerID, RedDays: redDays, RedFee: redFee, RedPaid: redPaid, Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#rojaInput").html(res.dataWeekAdminGameRedComment);
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdminGameDetailDocs(Season, Week, Game){
    //console.log('loadWeekAdminGameDetailDocs');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/Games/weekAdmin-ScheduleScores-GameDocuments.php',
		data: {Week: Week, Game: Game},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#gameDocInput").html(res.dataWeekAdminGameDocs);
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function readURLA4(input, Anexo4) {
	$("#previewMyAnexo4").html('');
	$("#previewMyAnexo4").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$("#anexo4_upload_form").ajaxForm({
		dataType:  'json', 
		success: showResponseMyAnexo4
	}).submit();

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + Anexo4)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + Anexo4).show();
		$('#' + Anexo4).width('100%').height('auto');
	}
}

function readURLA1(input, Anexo1) {
	$("#previewMyAnexo1").html('');
	$("#previewMyAnexo1").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$("#anexo1_upload_form").ajaxForm({
		dataType:  'json', 
		success: showResponseMyAnexo1
	}).submit();

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + Anexo1)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + Anexo1).show();
		$('#' + Anexo1).width('100%').height('auto');
	}
}

function readURLA2(input, Anexo2) {
	$("#previewMyAnexo2").html('');
	$("#previewMyAnexo2").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$("#anexo2_upload_form").ajaxForm({
		dataType:  'json', 
		success: showResponseMyAnexo2
	}).submit();

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + Anexo2)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + Anexo2).show();
		$('#' + Anexo2).width('100%').height('auto');
	}
}

function readURLA3(input, Anexo3) {
	$("#previewMyAnexo3").html('');
	$("#previewMyAnexo3").html('<img src="imagenes/loader.gif" alt="Uploading...." style="width: 150;"/>');
	$("#anexo3_upload_form").ajaxForm({
		dataType:  'json', 
		success: showResponseMyAnexo3
	}).submit();

	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + Anexo3)
				.attr('src', e.target.result)
		};
		reader.readAsDataURL(input.files[0]);
		$('#' + Anexo3).show();
		$('#' + Anexo3).width('100%').height('auto');
	}
}

/*****************************************************************************************************************
*************************************************Games Admin******************************************************
*****************************************************************************************************************/

/*****************************************************************************************************************
**********************************************Games Admin COACH***************************************************
*****************************************************************************************************************/

function loadWeeksAdminC(type){
    //console.log('loadWeeksAdminC');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/GamesCoach/changeWeeksAdmin.php',
		data: {Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataWeeksAdmin);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdminC(Week, team, type){
    //console.log('loadWeekAdmin week = ' + Week);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/GamesCoach/changeWeekAdmin.php',
		data: {Week: Week, Team:team, Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				if(type === 0){
				    loadWeekAdmminReloadListC(Week, team, type);
				}
				if(type === 1){
				    loadWeekAdmminReloadListTeamC(Week, team, type);
				}
				$("#weekAdminContent").html(res.dataWeekAdmin);
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdmminReloadListC(Week, team, type){
    //console.log('loadWeekReloadList week = ' + Week);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/GamesCoach/changeWeeksAdminReloadWeekSelector.php',
		data: {Week: Week, Team:team, Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#weekadminselectorsection").html(res.dataWeeks);
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdmminReloadListTeamC(Week, team, type){
    //console.log('loadWeekReloadListTeam week = ' + Week, team = ' + team, type = ' + type);
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/GamesCoach/changeWeeksAdminReloadWeekTeamSelector.php',
		data: {Week: Week, Team:team, Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#teamadminselectorsection").html(res.dataWeeks);
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdminGameCommentsC(Comment, GameID){
    //console.log('loadWeekAdminGameComments');
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/GamesCoach/weekAdmin-ScheduleScores-Comment.php',
		data: {GameID: GameID, Comment: Comment},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#comentarioInput").html(res.dataWeekAdminGameComment);
				$("#comentarioInput").toggle();
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function abrirFichaEditC(id, week, game, gamedesc,Comentarios, SQL){
	//console.log('abrirFichaEdit');
	var attr = $('#edit'+id).attr('style');
	$('.juego').css('display', 'none');
	$('#expandir'+id).attr('src', './imagenes/expandir.png');	
	$('.expandirButton').attr('src', './imagenes/expandir.png');
	// For some browsers, `attr` is undefined; for others,
	// `attr` is false.  Check for both.
	if (typeof attr == typeof undefined) {
		$('#edit'+id).css('display', 'none');					
		$('#expandir'+id).attr('src', './imagenes/expandir.png');
		$("#content" + id).html('');

	}else{
		$('#edit'+id).removeAttr('style');	
		$('#expandir'+id).attr('src', './imagenes/colapsar.png');
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Admin/GamesCoach/weekAdmin-ScheduleScoresGameDetail.php',
			data: {week: week, game: game, gamedesc: gamedesc, Comentarios: Comentarios, SQL: SQL},
			success: function (res) {
				mainLoadingOff()
				if (res.status === '1') {
					setWeekGameDetailHtml("#content" + id, res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
				console.log('Exception:' + exception);
			}
		});
	}
}

function abrirFichaEditSC(id, week, game, gamedesc, Comentarios, SQL){
	//console.log('abrirFichaEditS');
	var attr = $('#editS'+id).attr('style');
	$('.juegoS').css('display', 'none');
	$('#expandirS'+id+'SA').attr('src', './imagenes/expandir.png');	
	$('.expandirButtonS').attr('src', './imagenes/expandir.png');
	// For some browsers, `attr` is undefined; for others,
	// `attr` is false.  Check for both.
	if (typeof attr == typeof undefined) {
		$('#editS'+id).css('display', 'none');					
		$('#expandirS'+id).attr('src', './imagenes/expandir.png');
		$("#contentS" + id).html('');

	}else{
		$('#editS'+id).removeAttr('style');	
		$('#expandirS'+id+'SA').attr('src', './imagenes/colapsar.png');
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Admin/GamesCoach/weekAdmin-ScheduleScoresGameDetailS.php',
			data: {week: week, game: game, gamedesc: gamedesc, Comentarios: Comentarios, SQL: SQL},
			success: function (res) {
				mainLoadingOff()
				if (res.status === '1') {
					setWeekGameDetailHtml("#contentS" + id, res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
				console.log('Exception:' + exception);
			}
		});
	}
}

/*****************************************************************************************************************
**********************************************Games Admin REFEREE*************************************************
*****************************************************************************************************************/

function loadWeeksAdminR(type){
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/GamesReferee/changeWeeksAdmin.php',
		data: {Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#body").html(res.dataWeeksAdmin);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdminR(Week, team, type){
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/GamesReferee/changeWeekAdmin.php',
		data: {Week: Week, Team:team, Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				if(type === 0){
				    loadWeekAdmminReloadListR(Week, team, type);
				}
				if(type === 1){
				    loadWeekAdmminReloadListTeamR(Week, team, type);
				}
				$("#weekAdminContent").html(res.dataWeekAdmin);
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdmminReloadListR(Week, team, type){
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/GamesReferee/changeWeeksAdminReloadWeekSelector.php',
		data: {Week: Week, Team:team, Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#weekadminselectorsection").html(res.dataWeeks);
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdmminReloadListTeamR(Week, team, type){
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/GamesReferee/changeWeeksAdminReloadWeekTeamSelector.php',
		data: {Week: Week, Team:team, Type: type},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#teamadminselectorsection").html(res.dataWeeks);
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function loadWeekAdminGameCommentsR(Comment, GameID){
	mainLoadingOn();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: 'ajax/Admin/GamesReferee/weekAdmin-ScheduleScores-Comment.php',
		data: {GameID: GameID, Comment: Comment},
		success: function (res) {
			mainLoadingOff()
			if (res.status === '1') {
				$("#comentarioInput").html(res.dataWeekAdminGameComment);
				$("#comentarioInput").toggle();
			}else{
				console.log(res);
			}
		},
		error: function(jqxhr, status, exception) {
			mainLoadingOff();
			alert(MSG_AJAX_GENERIC);
			console.log('Exception:' + exception);
		}
	});
}

function abrirFichaEditR(id, week, game, gamedesc, lgoals, vgoals, Arbitro, Comentarios, Extral, Extrav, SQL){
	var attr = $('#edit'+id).attr('style');
	$('.juego').css('display', 'none');
	$('#expandir'+id).attr('src', './imagenes/expandir.png');	
	$('.expandirButton').attr('src', './imagenes/expandir.png');
	if (typeof attr == typeof undefined) {
		$('#edit'+id).css('display', 'none');					
		$('#expandir'+id).attr('src', './imagenes/expandir.png');
		$("#content" + id).html('');

	}else{
		$('#edit'+id).removeAttr('style');	
		$('#expandir'+id).attr('src', './imagenes/colapsar.png');
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Admin/GamesReferee/weekAdmin-ScheduleScoresGameDetail.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals, Arbitro: Arbitro, Comentarios: Comentarios, Extral: Extral, Extrav: Extrav, SQL: SQL},
			success: function (res) {
				mainLoadingOff()
				if (res.status === '1') {
					setWeekGameDetailHtml("#content" + id, res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
				console.log('Exception:' + exception);
			}
		});
	}
}

function abrirFichaEditSR(id, week, game, gamedesc, lgoals, vgoals, Arbitro, Comentarios, Extral, Extrav, SQL){
	var attr = $('#editS'+id).attr('style');
	$('.juegoS').css('display', 'none');
	$('#expandirS'+id+'SA').attr('src', './imagenes/expandir.png');	
	$('.expandirButtonS').attr('src', './imagenes/expandir.png');
	if (typeof attr == typeof undefined) {
		$('#editS'+id).css('display', 'none');					
		$('#expandirS'+id).attr('src', './imagenes/expandir.png');
		$("#contentS" + id).html('');

	}else{
		$('#editS'+id).removeAttr('style');	
		$('#expandirS'+id+'SA').attr('src', './imagenes/colapsar.png');
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Admin/GamesReferee/weekAdmin-ScheduleScoresGameDetailS.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals, Arbitro: Arbitro, Comentarios: Comentarios, Extral: Extral, Extrav: Extrav, SQL: SQL},
			success: function (res) {
				mainLoadingOff()
				if (res.status === '1') {
					setWeekGameDetailHtml("#contentS" + id, res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
				console.log('Exception:' + exception);
			}
		});
	}
}

function abrirFichaEditBasketR(id, week, game, gamedesc, lgoals, vgoals, Arbitro, Comentarios, Extral, Extrav, SQL){
	var attr = $('#edit'+id).attr('style');
	$('.juego').css('display', 'none');
	$('#expandir'+id).attr('src', './imagenes/expandir.png');
	$('.expandirButton').attr('src', './imagenes/expandir.png');
	if (typeof attr == typeof undefined) {
		$('#edit'+id).css('display', 'none');
		$('#expandir'+id).attr('src', './imagenes/expandir.png');
		$("#content" + id).html('');
	}else{
		$('#edit'+id).removeAttr('style');
		$('#expandir'+id).attr('src', './imagenes/colapsar.png');
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Admin/GamesReferee/weekAdmin-ScheduleScoresGameDetailBasket.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals, Arbitro: Arbitro, Comentarios: Comentarios, Extral: Extral, Extrav: Extrav, SQL: SQL},
			success: function (res) {
				mainLoadingOff();
				if (res.status === '1') {
					setWeekGameDetailHtml("#content" + id, res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
				console.log('Exception:' + exception);
			}
		});
	}
}

function abrirFichaEditSBasketR(id, week, game, gamedesc, lgoals, vgoals, Arbitro, Comentarios, Extral, Extrav, SQL){
	var attr = $('#editS'+id).attr('style');
	$('.juegoS').css('display', 'none');
	$('#expandirS'+id+'SA').attr('src', './imagenes/expandir.png');
	$('.expandirButtonS').attr('src', './imagenes/expandir.png');
	if (typeof attr == typeof undefined) {
		$('#editS'+id).css('display', 'none');
		$('#expandirS'+id).attr('src', './imagenes/expandir.png');
		$("#contentS" + id).html('');
	}else{
		$('#editS'+id).removeAttr('style');
		$('#expandirS'+id+'SA').attr('src', './imagenes/colapsar.png');
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Admin/GamesReferee/weekAdmin-ScheduleScoresGameDetailSBasket.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals, Arbitro: Arbitro, Comentarios: Comentarios, Extral: Extral, Extrav: Extrav, SQL: SQL},
			success: function (res) {
				mainLoadingOff();
				if (res.status === '1') {
					setWeekGameDetailHtml("#contentS" + id, res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
				console.log('Exception:' + exception);
			}
		});
	}
}

function abrirFichaEditVoleibolR(id, week, game, gamedesc, lgoals, vgoals, Arbitro, Comentarios, Extral, Extrav, SQL){
	var attr = $('#edit'+id).attr('style');
	$('.juego').css('display', 'none');
	$('#expandir'+id).attr('src', './imagenes/expandir.png');
	$('.expandirButton').attr('src', './imagenes/expandir.png');
	if (typeof attr == typeof undefined) {
		$('#edit'+id).css('display', 'none');
		$('#expandir'+id).attr('src', './imagenes/expandir.png');
		$("#content" + id).html('');
	}else{
		$('#edit'+id).removeAttr('style');
		$('#expandir'+id).attr('src', './imagenes/colapsar.png');
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Admin/GamesReferee/weekAdmin-ScheduleScoresGameDetailVoleibol.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals, Arbitro: Arbitro, Comentarios: Comentarios, Extral: Extral, Extrav: Extrav, SQL: SQL},
			success: function (res) {
				mainLoadingOff();
				if (res.status === '1') {
					setWeekGameDetailHtml("#content" + id, res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
				console.log('Exception:' + exception);
			}
		});
	}
}

function abrirFichaEditSVoleibolR(id, week, game, gamedesc, lgoals, vgoals, Arbitro, Comentarios, Extral, Extrav, SQL){
	var attr = $('#editS'+id).attr('style');
	$('.juegoS').css('display', 'none');
	$('#expandirS'+id+'SA').attr('src', './imagenes/expandir.png');
	$('.expandirButtonS').attr('src', './imagenes/expandir.png');
	if (typeof attr == typeof undefined) {
		$('#editS'+id).css('display', 'none');
		$('#expandirS'+id).attr('src', './imagenes/expandir.png');
		$("#contentS" + id).html('');
	}else{
		$('#editS'+id).removeAttr('style');
		$('#expandirS'+id+'SA').attr('src', './imagenes/colapsar.png');
		mainLoadingOn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'ajax/Admin/GamesReferee/weekAdmin-ScheduleScoresGameDetailSVoleibol.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals, Arbitro: Arbitro, Comentarios: Comentarios, Extral: Extral, Extrav: Extrav, SQL: SQL},
			success: function (res) {
				mainLoadingOff();
				if (res.status === '1') {
					setWeekGameDetailHtml("#contentS" + id, res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				mainLoadingOff();
				alert(MSG_AJAX_GENERIC);
				console.log('Exception:' + exception);
			}
		});
	}
}

function azFlyerLang(key, replacements) {
	var text = (typeof LANG_FLYER_FB !== 'undefined' && LANG_FLYER_FB[key]) ? LANG_FLYER_FB[key] : '';
	if (!text) {
		return '';
	}
	if (replacements && replacements.length) {
		for (var i = 0; i < replacements.length; i++) {
			text = text.split('%' + (i + 1)).join(String(replacements[i]));
		}
	}
	return text;
}

/**
 * Facebook: one popup prepares images and opens the system share sheet (pick Facebook).
 * type: jornada | categoria | juego
 */
function azShareFlyersFacebook(type, jornadaId, categoriaId, juegoId) {
	var q = 'type=' + encodeURIComponent(type)
		+ '&Jornada_ID=' + encodeURIComponent(jornadaId)
		+ '&Categoria_ID=' + encodeURIComponent(categoriaId)
		+ '&Juego_ID=' + encodeURIComponent(juegoId);
	var launchUrl = new URL('pdf/flyerFacebookShareLaunch.php?' + q, window.location.href).href;
	var popW = 420;
	var popH = 520;
	var popLeft = Math.max(0, Math.round((window.screen.width - popW) / 2));
	var popTop = Math.max(0, Math.round((window.screen.height - popH) / 2));
	var popFeatures = 'popup=yes,width=' + popW + ',height=' + popH
		+ ',left=' + popLeft + ',top=' + popTop
		+ ',menubar=no,toolbar=no,location=no,status=no,resizable=yes,scrollbars=no';
	var w = window.open(launchUrl, 'azFbShare', popFeatures);
	if (!w) {
		alert(azFlyerLang('jsfb08') || MSG_AJAX_GENERIC);
	}
}

/*****************************************************************************************************************
**********************************************Games Admin COACH***************************************************
*****************************************************************************************************************/