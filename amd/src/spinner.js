define([], function() {
    return {
        init: function() {
            var iframeElem = document.getElementById('block_onlinesurvey_contentframe');
            var contentElem = document.getElementById('block_onlinesurvey_surveys_content');

            iframeElem.addEventListener('load', function() {
                contentElem.className = contentElem.className.replace(/block_onlinesurvey_is-loading/, '');
            }, true);
        }
    };
});
