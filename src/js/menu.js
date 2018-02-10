/*jslint browser */
(function () {
    "use strict";

    var active;
    var menu = document.getElementsByClassName("menu")[0].getElementsByTagName("li");

    function click(targetElement) {
        if (active) {
            active.classList.remove("active");
        }

        active = targetElement;
        active.classList.add("active");
    }

    function addHandler(i) {
        return function () {
            click(menu[i]);
        };
    }

    var i = menu.length;
    while (i) {
        i -= 1;
        menu[i].addEventListener("mousedown", addHandler(i));
    }

    var initial = window.location.hash.substr(1);
    if (initial) {
        i = menu.length;
        while (i) {
            i -= 1;

            if (initial === menu[i].parentNode.href.split("#").pop()) {
                active = menu[i];
                active.classList.add("active");
                return;
            }
        }
    }
}());
