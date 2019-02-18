define(['core/templates', 'core/str'], function(templates, str) {

	var modalTitle = '';
	var modalZoomSelector = '#block_onlinesurvey_surveys_content';
	
	var popupinfotitle = '';
	var popupinfocontent = '';
	var userlogintime = 0;
	
	var doRefresh = function() {
		var myElement = document.getElementById("block_onlinesurvey_contentframe");
		if(myElement){
			var oldsrc = myElement.src;
			myElement.src = '';
			myElement.src = oldsrc;
		}
	}
	
	var handleClickSmallModal = function(e) {
		e.preventDefault();

		var modalZoomElem = e.target;

		var originalIframe = modalZoomElem.querySelector('iframe');

		// #8984
		var templatePromise = null;


        if (originalIframe !== null) {
        	// open from Moodle page, i.e., onlinesurvey iframe exists
            templatePromise = templates.render('block_onlinesurvey/modal-iframe', {
                // copy iframe target URL from block, but inform that now in modalZoom window
            	src: originalIframe.src + "&modalZoom=1",
                title: modalTitle
            });
        } else {
            // open from iframe, i.e., needs to switch to parent Moodle page
            originalIframe = parent.document.querySelector('iframe');
            templatePromise = templates.render('block_onlinesurvey/modal-iframe', {
                // copy iframe target URL from block, but inform that now in modalZoom window
            	src: originalIframe.src + "&modalZoom=1",
                title: modalTitle
            });
        }
        // END #8984

		templatePromise.done(function(source, javascript) {

			var div = document.createElement('div');
			div.innerHTML = source;

			var modalContainer = div.firstChild;

			document.body.insertBefore(modalContainer, document.body.firstChild);
			document.body.className += ' block_onlinesurvey_custom-modal-open';

			var closeCallback = function(e) {

				var target = e.target

				document.body.className = document.body.className.replace(' block_onlinesurvey_custom-modal-open', '');

				doRefresh();

				modalContainer.className += ' fading';

				setTimeout(function() {
					if(modalContainer.parentNode !== null) {
						modalContainer.parentNode.removeChild(modalContainer);
					}
				}, 250);
			}

			modalContainer.querySelector('.block_onlinesurvey_custom-modal_close-button')
				.addEventListener('click', function(e) {
					e.preventDefault();
					return closeCallback(e);
				});

			modalContainer.addEventListener('click', function(e) {
				e.preventDefault();

				return e.target !== modalContainer ?
					false : closeCallback(e);
			});
		});
	}
	
	return {
		init: function(popuptitle, popupcontent, currentlogin) {

			popupinfotitle = popuptitle;
			popupinfocontent = popupcontent;
			userlogintime = currentlogin;
			
			var zoomContainer = document.querySelectorAll(modalZoomSelector);

			for	(var i=0; i<zoomContainer.length; i++) {
				zoomContainer[i].addEventListener('click', handleClickSmallModal);
			}
			
			// namespace in window for EVASYS-functions etc
            window.EVASYS = {
                // define "global" functions in namespace -> later "external" access from iframe possible
            		generate_popupinfo: this.generate_popupinfo
            };
            window.evasysGeneratePopupinfo = this.generate_popupinfo;
		},
		generate_popupinfo: function() {
			
			// Get saved data from sessionStorage
			var popupinfo = sessionStorage.getItem('onlinesurvey_popupinfo');
			
			if(popupinfo == false || popupinfo == null || popupinfo != userlogintime){
				
				// Save data to sessionStorage
				sessionStorage.setItem('onlinesurvey_popupinfo', userlogintime);
				
				var templatePromise = templates.render('block_onlinesurvey/popupinfo', {
					title: popupinfotitle,
					content: popupinfocontent
				});
				
				templatePromise.done(function(source, javascript) {
					
					var div = document.createElement('div');
					div.innerHTML = source;
					
					var modalContainer = div.firstChild;
					
					document.body.insertBefore(modalContainer, document.body.firstChild);
					document.body.className += ' block_onlinesurvey_custom-modal-open popupinfo';
					
					var closeCallback = function(e) {
						
						var target = e.target
						
						document.body.className = document.body.className.replace(' block_onlinesurvey_custom-modal-open', '');
						
						modalContainer.className += ' fading';
						
						setTimeout(function() {
							if(modalContainer.parentNode !== null) {
								modalContainer.parentNode.removeChild(modalContainer);
							}
						}, 250);
					}
					
					modalContainer.querySelector('.block_onlinesurvey_custom-modal_close-button')
					.addEventListener('click', function(e) {
						e.preventDefault();
						return closeCallback(e);
					});
					
					modalContainer.addEventListener('click', function(e) {
						e.preventDefault();
						
						return e.target !== modalContainer ?
								false : closeCallback(e);
					});
				});
			}
		}
	}
});
