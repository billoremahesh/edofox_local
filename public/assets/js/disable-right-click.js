// Disable right click
document.addEventListener('contextmenu', event => event.preventDefault());

// Ref: https://stackoverflow.com/questions/28690564/is-it-possible-to-remove-inspect-element
// To disable inspector via keyboard
// That said, if you are reading this, you already know how to go into inspector via browser.
// Please let us know if you find any security vulnerability in our code
// We are humans too :)
document.onkeydown = function(e) {
    if (event.keyCode == 123) {
        return false;
    }
    if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
        return false;
    }
    if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
        return false;
    }
    if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
        return false;
    }
    if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
        return false;
    }
}