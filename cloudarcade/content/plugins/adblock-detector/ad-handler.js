document.addEventListener("DOMContentLoaded", function() {
	window.setTimeout(function() {
		checkAdBlocker();
	}, 100);
	function adBlockDetected() {
		let elem = document.getElementById('adblock-detector-plugin');
		if(elem){
			elem.style.display = 'block';
		}
	}
	function checkAdBlocker() {
		let fakeAd = document.createElement("div"); 
		fakeAd.className =  "textads banner-ads banner_ads ad-unit ad-zone ad-space adsbox"
		fakeAd.style.height = "1px"
		document.body.appendChild(fakeAd) 
		let x_width = fakeAd.offsetHeight; 
		let msg = document.getElementById("msg");
		if(x_width){ 
			//
		} else { 
			adBlockDetected();
		}
		fakeAd.remove();
	}
});