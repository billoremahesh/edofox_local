/**
 * 1. https://stackoverflow.com/questions/18261214/load-external-js-file-in-another-js-file/18261253
 * 2. https://stackoverflow.com/questions/41289602/add-defer-or-async-attribute-to-dynamically-generated-script-tags-via-javascript
 */

var imported_1 = document.createElement('script');
imported_1.src = 'https://polyfill.io/v3/polyfill.min.js?features=es6';
document.head.appendChild(imported_1);


MathJax = {
    tex: {
        inlineMath: [
            ['$', '$'],
            ['\\(', '\\)']
        ]
    },
    startup: {
        ready: function () {
            MathJax.startup.defaultReady();
            document.getElementById('render').disabled = false;
        }
    }
}


var imported_2 = document.createElement('script');
imported_2.src = 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js';
imported_2.defer = true;
imported_2.id = "MathJax-script";
document.head.appendChild(imported_2);