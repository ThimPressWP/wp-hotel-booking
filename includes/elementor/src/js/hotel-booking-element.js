const jQuerydynamicGallery = document.getElementById("hb-room-thumbnail");
const jQuerydynamicgal = document.querySelector('.dynamic-gal');
if (jQuerydynamicgal) {
	const data = jQuerydynamicgal.getAttribute('data-dynamicpath');
	const objs = JSON.parse(data);
	const modifiedData = objs.map(obj => { 
		return {
			src: obj.src,
			responsive: obj.responsive,
			thumb: obj.thumb,
			subHtml: obj.subHtml
		};
	});
	const dynamicGallery = window.lightGallery(jQuerydynamicGallery, {  
		dynamic: true,
		plugins: [lgThumbnail],
		dynamicEl: modifiedData,
		height: "80%",
		width: "65%",
		addClass: 'fixed-size',
		download: false,
		counter: true,
		// thumbWidth: "65%",
		// enableThumbSwipe:true,
		
	});
	document.querySelectorAll(".dynamic-gal").forEach((el, index) => {
		el.addEventListener("click", () => {
			dynamicGallery.openGallery(0);
		});
	});
}