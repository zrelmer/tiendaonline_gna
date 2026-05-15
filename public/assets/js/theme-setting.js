/*=====================
      Color Picker
==========================*/
(function () {
    var colorPickEl = document.getElementById("colorPick");
    if (colorPickEl) {
        var color_picker1 = colorPickEl.value;
        colorPickEl.onchange = function () {
            color_picker1 = this.value;
            document.body.style.setProperty("--theme-color", color_picker1);
            document.body.style.setProperty("--theme-color-rgb", color_picker1);
        };
    }

    /*========================
     Dark setting js
     ==========================*/
    $("#darkButton").on("click", function () {
        $("body").removeClass("light");
        $("body").addClass("dark");
        var colorLink = document.getElementById("color-link");
        if (colorLink) {
            colorLink.setAttribute("href", "../assets/css/dark.css");
        }
    });

    $("#lightButton").on("click", function () {
        $("body").removeClass("dark");
        $("body").addClass("light");
        var colorLink = document.getElementById("color-link");
        if (colorLink) {
            colorLink.setAttribute("href", "../assets/css/style.css");
        }
    });

    /*========================
       RTL setting js
       ==========================*/
    $(".rtl").on("click", function () {
        if ($("body").hasClass("ltr")) {
            $("html").attr("dir", "rtl");
            $("body").removeClass("ltr");
            $("body").addClass("rtl");
            $("#rtl-link").attr("href", "../assets/css/vendors/bootstrap.rtl.css");
        } else {
            $("html").attr("dir", "");
            $("body").removeClass("rtl");
            $("body").addClass("ltr");
            $("#rtl-link").attr("href", "../assets/css/vendors/bootstrap.css");
        }
    });
})();