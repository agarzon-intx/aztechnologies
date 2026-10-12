/* Players list: the "complete" icon opens the player's picture and ID document. */
$(document).on('click', '.player-docs-ok', function (e) {
	e.preventDefault();
	e.stopPropagation();
	var $i = $(this), id = parseInt($i.data('id'), 10), type = String($i.data('idtype') || ''), t = Date.now();
	if (!id) {
		return;
	}
	var esc = function (s) { return $('<div>').text(String(s || '')).html(); };
	var box = 'flex: 1 1 260px; min-width: 0; text-align: center;';
	var label = function (s) { return '<div style="font-weight: 600; margin-bottom: 6px;">' + esc(s) + '</div>'; };
	var idHtml;
	if (type === 'pdf') {
		idHtml = '<iframe src="Form/fetch_player_pdf.php?Jugador_ID=' + id + '&tmp=' + t + '" style="width: 100%; height: 420px; border: 1px solid #ddd; border-radius: 6px;"></iframe>'
			+ '<div style="margin-top: 4px;"><a href="Form/fetch_player_pdf.php?Jugador_ID=' + id + '&tmp=' + t + '" target="_blank" rel="noopener">PDF</a></div>';
	} else if (type === 'img') {
		idHtml = '<a href="Form/fetch_image.php?Jugador_ID=' + id + '&Imagen=Identificacion&tmp=' + t + '" target="_blank" rel="noopener">'
			+ '<img src="Form/fetch_image.php?Jugador_ID=' + id + '&Imagen=Identificacion&tmp=' + t + '" alt="" style="max-width: 100%; max-height: 420px; border-radius: 6px;"></a>';
	} else {
		idHtml = '<div style="color: #888; padding: 40px 0;">-</div>';
	}
	var html = '<div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start;">'
		+ '<div style="' + box + ' flex-basis: 200px; flex-grow: 0;">' + label($i.data('lfoto'))
		+ '<img src="Form/fetch_image.php?Jugador_ID=' + id + '&Imagen=Foto&tmp=' + t + '" alt="" style="width: 200px; max-width: 100%; border-radius: 6px; background: #BFBFBF;"></div>'
		+ '<div style="' + box + '">' + label($i.data('lid')) + idHtml + '</div>'
		+ '</div>';
	if (typeof Swal !== 'undefined') {
		Swal.fire({ title: esc($i.data('name')), html: html, width: 900, showCloseButton: true, showConfirmButton: false, focusConfirm: false });
	} else {
		window.open('Form/fetch_image.php?Jugador_ID=' + id + '&Imagen=Foto&tmp=' + t, '_blank');
	}
});

