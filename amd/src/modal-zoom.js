define(['jquery', 'core/templates', 'core/modal', 'core/modal_factory', 'core/modal_events'],
    function($, templates, Modal, ModalFactory, ModalEvents) {

    var modalTitle = '';
    var modalZoomSelector = '#block_onlinesurvey_surveys_content';

    var popupinfotitle = '';
    var popupinfocontent = '';
    var userlogintime = 0;

    var handleClickSmallModal = function(e) {
        e.preventDefault();

        var modalZoomElem = e.target;

        var originalIframe = modalZoomElem.querySelector('iframe');

        var modalPromise = null;
        var templatePromise = null;
        modalPromise = ModalFactory.create({type: ModalFactory.types.DEFAULT, large: true, title: modalTitle});
        if (originalIframe !== null) {
            // Open from Moodle page, i.e., onlinesurvey iframe exists.
            templatePromise = templates.render('block_onlinesurvey/modal-iframe', {
                // Copy iframe target URL from block, but inform that now in modalZoom window.
                src: originalIframe.src + "&modalZoom=1",
                title: modalTitle
            });
        } else {
            // Open from iframe, i.e., needs to switch to parent Moodle page.
            originalIframe = parent.document.querySelector('iframe');
            templatePromise = templates.render('block_onlinesurvey/modal-iframe', {
                // Copy iframe target URL from block, but inform that now in modalZoom window.
                src: originalIframe.src + "&modalZoom=1",
                title: modalTitle
            });
        }

        $.when(templatePromise, modalPromise).done(function(source, iframemodal) {
            iframemodal.setBody(source);
            iframemodal.getModal().addClass('modal-xl');

            iframemodal.show();
        });
    };

    return {
        init: function(popuptitle, popupcontent, currentlogin) {

            popupinfotitle = popuptitle;
            popupinfocontent = popupcontent;
            userlogintime = currentlogin;

            var zoomContainer = document.querySelectorAll(modalZoomSelector);

            for (var i = 0; i < zoomContainer.length; i++) {
                zoomContainer[i].addEventListener('click', handleClickSmallModal);
            }

            // Namespace in window for EVASYS-functions etc.
            window.EVASYS = {
                // Define "global" functions in namespace -> later "external" access from iframe possible.
                generatepopupinfo: this.generatepopupinfo
            };
            window.evasysGeneratePopupinfo = this.generatepopupinfo;
        },
        generatepopupinfo: function() {

            // Get saved data from sessionStorage.
            var popupinfo = sessionStorage.getItem('onlinesurvey_popupinfo');

            if (popupinfo == false || popupinfo === null || popupinfo != userlogintime) {

                // Save data to sessionStorage.
                sessionStorage.setItem('onlinesurvey_popupinfo', userlogintime);

                var modalPromise = ModalFactory.create(
                    {
                        type:ModalFactory.types.DEFAULT,
                        body: popupinfocontent,
                        title: popupinfotitle,
                        large: true
                    }
                );
                $.when(modalPromise).then(function(popupmodal) {
                    popupmodal.getModal().addClass('modal-xl');
                    popupmodal.show();
                });
            }
        }
    };
});
