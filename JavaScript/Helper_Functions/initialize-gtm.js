const gtmID = 'GTM-XXXXXXX'; //YOUR GTM CONTAINER ID
const prodDomain = 'example.com'; //Your production domain

if (window.location.href.indexOf(prodDomain) !== -1) {
	initializeGTM(gtmID);
}

/**
 * Initializes the GTM function
 */
function initializeGTM(containerID) {
	(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer',containerID);
}