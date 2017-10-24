try{
	var docEl = document.documentElement,
	resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
	recalc = function () {
		var clientWidth = docEl.clientWidth;
		if (!clientWidth) return;
		docEl.style.fontSize = 100 * (clientWidth / 750) + 'px';
	};
	recalc();
	if (document.addEventListener){
		window.addEventListener(resizeEvt, recalc, false);
	    document.addEventListener('DOMContentLoaded', recalc, false);
	}
} catch (e) {}