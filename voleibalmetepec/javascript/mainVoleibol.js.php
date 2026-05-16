

function abrirFichaVoleibol(id, week, game, gamedesc, lgoals, vgoals){
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
			url: 'ajax/Content/week-ScheduleScoresGameDetailVoleibol.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals},
			success: function (res) {
				mainLoadingOff()
				if (res.status === '1') {
					$("#content" + id).html(res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				console.log('Exception:' + exception);
			}
		});
	}
}

function abrirFichaSVoleibol(id, week, game, gamedesc, lgoals, vgoals){
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
			url: 'ajax/Content/week-ScheduleScoresGameDetailSVoleibol.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals},
			success: function (res) {
				mainLoadingOff()
				if (res.status === '1') {
					$("#content" + id + 'S').html(res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				console.log('Exception:' + exception);
			}
		});
	}
}

function abrirFichaEditVoleibol(id, week, game, gamedesc, lgoals, vgoals, Arbitro, Comentarios, Extral, Extrav, SQL){
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
			url: 'ajax/Admin/Games/weekAdmin-ScheduleScoresGameDetailVoleibol.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals, Arbitro: Arbitro, Comentarios: Comentarios, Extral: Extral, Extrav: Extrav, SQL: SQL},
			success: function (res) {
				mainLoadingOff()
				if (res.status === '1') {
					$("#content" + id).html(res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
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
					$("#contentS" + id).html(res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				console.log('Exception:' + exception);
			}
		});
	}
}

function abrirFichaEditSVoleibol(id, week, game, gamedesc, lgoals, vgoals, Arbitro, Comentarios, Extral, Extrav, SQL){
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
			url: 'ajax/Admin/Games/weekAdmin-ScheduleScoresGameDetailSVoleibol.php',
			data: {week: week, game: game, gamedesc: gamedesc, lgoals: lgoals, vgoals: vgoals, Arbitro: Arbitro, Comentarios: Comentarios, Extral: Extral, Extrav: Extrav, SQL: SQL},
			success: function (res) {
				mainLoadingOff()
				if (res.status === '1') {
					$("#contentS" + id).html(res.dataWeekGameDetail);
				}
			},
			error: function(jqxhr, status, exception) {
				console.log('Exception:' + exception);
			}
		});
	}
}