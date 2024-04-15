define(['core/str'], function(Str) {
    return {
        init: function() {
            var iframeElem = document.getElementById('block_onlinesurvey_contentframe');
            var that = this;
            console.log('compactview init. Got iframeElem:', iframeElem);
            iframeElem.addEventListener('DOMContentLoaded', (event) => {
                console.log('iframe DOMContentLoaded');
                console.log('event:', event);
                that.showcompact(event);
            });
            iframeElem.addEventListener('readystatechange', (event) => {
                console.log('iframe readystatechange');
                console.log('event:', event);
                that.showcompact(event);
            });
            iframeElem.addEventListener('load', (event) => {
                console.log('iframe load');
                console.log('event:', event);
                that.showcompact(event);
            });
        },
        showcompact: function (event) {
            var content = document.documentElement.innerHTML;
            console.log('called showcompact');
            console.log('event:', event);
            console.log('content:', content);
            console.log('document:', document);
            console.log('document.parent:', document.parent);
            console.log('window:', window);
            console.log('window.parent:', window.parent);
            // $surveycount = preg_match_all($re, $content2, $matches, PREG_SET_ORDER, 0);
            // if (isset($config->lti_regex_instructor) && !empty($config->lti_regex_instructor)) {
            //     $reinstructor = $config->lti_regex_instructor;
            //
            //     // No regex in config -> use default regex.
            // } else {
            //     $reinstructor = BLOCK_ONLINESURVEY_LTI_REGEX_INSTRUCTOR_DEFAULT;
            // }
            // $surveycount += preg_match_all($reinstructor, $content2, $matches, PREG_SET_ORDER, 0);
        }
    }
});