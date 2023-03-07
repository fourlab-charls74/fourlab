

(function ($) {

    'use strict';

    function initInputFocus(){
        $(".input_box").on("click", function(){
            $(this).find("input").focus();
        });

    }
    function initActiveMenu() {
        // === following js will activate the menu in left side bar based on url ====
        $(".side_menu a").each(function () {
            var pageUrl = window.location.href.split(/[?#]/)[0];
            var pageUrl2 = pageUrl.split(/[/]/);
            var pageUrls="";
            for(var i = 0; i <= 5; i++){
                if(i !== 0){
                    pageUrls += "/"+pageUrl2[i];
                }else{
                    pageUrls += pageUrl2[i];
                }
            }
            if (this.href == pageUrls || this.href == pageUrl) {
                $(this).addClass("active");
                $(this).parent().addClass("p-active"); // add active to li of the current link
                $(this).parent().parent().addClass("p-show");
                $(this).parent().parent().prev().addClass("p-active"); // add active class to an anchor
                $(this).parent().parent().parent().addClass("p-active");
                $(this).parent().parent().parent().parent().addClass("p-show"); // add active to li of the current link
                $(this).parent().parent().parent().parent().parent().addClass("p-active");
                $(".mobile_header ul li:eq(" + $(this).parents(".side_menu").find("> ul .p-active").index()+")").addClass("on");
                function subGnbMove(){
                    if($("header .mobile_header > ul > li.on").length > 0){
                        var gbContent = $(".mobile_header").width();
                        var thisEl = $(".mobile_header > ul > li.on a").position().left + $(".mobile_header > ul > li.on a").parent().width() / 2;
                        var pos = (thisEl + $(".mobile_header > ul").scrollLeft()) - (gbContent / 2);
                        $(".mobile_header > ul").animate({
                            scrollLeft: pos
                        }, 200);
                    }
                }subGnbMove();
                $(".mobile_sub_list .now_page").show();
                $(".mobile_sub_list .now_page span").text($(this).text());
                var html = $(this).parents(".side_menu").find("> ul > .p-active > a").next().html();
                if(html == undefined){
                    $(".now_page").addClass("arrow_none")
                }else{
                    $(".mobile_sub_list ul").append(html);
                }
                return false;
            }else{
                $(".mobile_sub_list .now_page").hide();
            }
        });
        
        $(".side_menu .arrow").on("click", function(){
            if(!$("body").hasClass("sidebar-enable")){
                if(!$(this).parent().hasClass("p-active")){
                    $(this).parent().parent().find("> li > ul").hide();
                    $(this).parent().parent().find("> li").removeClass("p-active");
                    $(this).parent().addClass("p-active");
                    $(this).next().slideDown(200);
                }else{
                    $(this).parent().removeClass("p-active");
                    $(this).next().slideUp(200);
                }
            }else{
                if(!$(this).parent().hasClass("depth2 p-active")){
                    $(".depth2").removeClass("p-active");
                    $(".depth2 .arrow").next().hide();
                    $(this).next().slideDown(200);
                    $(this).parent().addClass("p-active");
                }else{
                    $(this).next().slideUp(200);
                    $(this).parent().removeClass("p-active");
                }
            }
            return false;
        });
        
        $(".top_setting_btn .side").on("click", function(){
            if($(window).outerWidth() >= 720){
                if($("body").hasClass("sidebar-enable")){
                    $("body").removeClass("sidebar-enable");
                    $(this).removeClass("side_off");
                }else{
                    $("body").addClass("sidebar-enable");
                    $(this).addClass("side_off");
                }
            }else{
                if($("body").hasClass("mobile-side")){
                    $("body").removeClass("mobile-side");
                    $("body").addClass("sidebar-enable");
                }else{
                    $("body").addClass("mobile-side");
                    $("body").removeClass("sidebar-enable");
                }
            }
            return false;
        });

        $(".mobile_sub_list .now_page").on("click", function(){
            if(!$(this).hasClass("act")){
                $(this).addClass("act");
                $(this).next().slideDown(200);
            }else{
                $(this).removeClass("act");
                $(this).next().slideUp(200);
            }
            return false;
        });
    }
    function initFullScreen() {
        $('[data-toggle="fullscreen"]').on("click", function (e) {
            e.preventDefault();
            $('body').toggleClass('fullscreen-enable');
            if (!document.fullscreenElement && /* alternative standard method */ !document.mozFullScreenElement && !document.webkitFullscreenElement) {  // current working methods
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
            } else {
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                }
            }
        });
        document.addEventListener('fullscreenchange', exitHandler );
        document.addEventListener("webkitfullscreenchange", exitHandler);
        document.addEventListener("mozfullscreenchange", exitHandler);
        function exitHandler() {
            if (!document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
                $('body').removeClass('fullscreen-enable');
            }
        }
    }
    function initUtil(){
    
        var now = new Date();
        var year= now.getFullYear();
        if(!$(".docs-date").hasClass("month")){
            $('.docs-datepicker .docs-date').datepicker({
                days: ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"],
                daysShort: ["일", "월", "화", "수", "목", "금", "토"],
                daysMin: ["일", "월", "화", "수", "목", "금", "토"],
                months: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
                monthsShort: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
                date: 'year',
                format: 'yyyy-mm-dd',
                autoHide: true,
                language: "kr",
                Readonly: true
            });
        }else{
            $('.docs-datepicker .docs-date').datepicker({
                months: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
                monthsShort: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
                date: 'month',
                format: 'yyyy-mm',
                autoHide: true,
                language: "kr",
                Readonly: true
            });
        }
        $(".docs-datepicker-trigger").on("click",function(e){
            e.stopPropagation();
            $(this).parents(".docs-datepicker").find(".docs-date").trigger("focus");
        });
        $('[data-toggle="tooltip"]').tooltip();
    }
    function initSettings() {
        if (window.sessionStorage) {
            var alreadyVisited = sessionStorage.getItem("is_visited");
            if (!alreadyVisited) {
                sessionStorage.setItem("is_visited", "light-mode");
                $(".top_setting_btn .mode").attr("class","mode app-light");
                $("#div-gd, #goods-class-grid").attr("class","ag-theme-balham");
                updateThemeSetting(false, true);
            } else {
                if(alreadyVisited === "light-mode") {
                    $(".top_setting_btn .mode").attr("class","mode app-light");
                    $("#div-gd, #goods-class-grid").attr("class","ag-theme-balham");
                    updateThemeSetting(false, true);
                } else if(alreadyVisited === "dark-mode") {
                    $(".top_setting_btn .mode").attr("class","mode app-dark");
                    $("#div-gd, #goods-class-grid, .dark-grid").attr("class","ag-theme-balham-dark");
                    updateThemeSetting(true, false);
                } 
            }
        }

        $(".top_setting_btn .mode").on("click", function(e) {
            if($(this).hasClass("app-light")) {
                $(this).attr("class","mode app-dark");
                $("#div-gd, #goods-class-grid, .dark-grid").attr("class","ag-theme-balham-dark");
                $(".darkmode").attr("class","ag-theme-balham-dark darkmode");
                sessionStorage.setItem("is_visited", "dark-mode");
                updateThemeSetting(true, false);
            } else if($(this).hasClass("app-dark")) {
                $(this).attr("class","mode app-light");
                $("#div-gd, #goods-class-grid").attr("class","ag-theme-balham");
                $(".darkmode").attr("class","ag-theme-balham darkmode");
                sessionStorage.setItem("is_visited", "light-mode");
                updateThemeSetting(false, true);
            }
            return false;
        });
    }

    function updateThemeSetting(light, dark) {
        $("#bootstrap-light").prop("disabled", light);
        $("#bootstrap-dark").prop("disabled", dark);
        $("#app-light").prop("disabled", light);
        $("#app-dark").prop("disabled", dark);
    }
function init() {
    initActiveMenu();
    initInputFocus();
    initFullScreen();
    initUtil();
    initSettings();
}

init();

})(jQuery)