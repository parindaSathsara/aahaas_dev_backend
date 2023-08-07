<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" /> -->

        <base href="/">
        <link rel="icon" href="../../public/favicon.ico" />
        <link crossorigin="use-credentials" rel="manifest" href="./manifest.json" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
        <script src="https://kit.fontawesome.com/d3afaf8272.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />

        <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
        <!-- Font awesome 5 -->
        <link href="fonts/fontawesome/css/all.min.css" type="text/css" rel="stylesheet">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.1.3/TweenMax.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>


        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#000000" />
        <meta name="description" content="Web site created using create-react-app" />
        <link rel="apple-touch-icon" href="../../public/logo192.png" />

        <!-- <link rel="manifest" href="%PUBLIC_URL%/manifest.json" /> -->
        <!-- @viteReactRefresh
        @vite(['resources/css/app.css','resources/js/app.jsx']) -->
</head>

<body onload="loadSideBar()">
        <div id="root"></div>
        <!-- <script type="module">
                import RefreshRuntime from "/@react-refresh"
                RefreshRuntime.injectIntoGlobalHook(window)
                window.$RefreshReg$ = () => {}
                window.$RefreshSig$ = () => (type) => type
                window.__vite_plugin_react_preamble_installed__ = true
        </script> -->
        <script>
                const global = globalThis;
        </script>


        <script>
                // $(function() {
                //         $("ul.dropdown-menu [data-toggle='dropdown']").on("mouseover", function(event) {
                //                 event.preventDefault();
                //                 event.stopPropagation();
                //                 //method 2: remove show from all siblings of all your parents
                //                 $(this).parents('.dropdown-submenu').siblings().find('.show').removeClass("show");
                //                 $(this).siblings().toggleClass("show");

                //                 // $(this).addClass('toggle-class [data-toggle='dropdown_menu')
                //                 //collapse all after nav is closed
                //                 $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
                //                         $('.dropdown-submenu .show').removeClass("show");
                //                 });
                //         });
                // });
        </script>

        <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3x9uy0MRBuz4McmPvm-tRCjvq8VgFKOg&libraries=places&callback=initMap">
        </script>

        <script>
                function loadSideBar() {


                        const showNavbar = (toggleId, logoId, navId, bodyId, headerId) => {
                                const toggle = document.getElementById(toggleId),
                                        nav = document.getElementById(navId),
                                        bodypd = document.getElementById(bodyId),
                                        headerpd = document.getElementById(headerId),
                                        logoImg = document.getElementById(logoId)

                                // Validate that all variables exist
                                if (toggle && nav && bodypd && headerpd && logoImg) {
                                        toggle.addEventListener('click', () => {

                                                nav.classList.toggle('sideBarShow')

                                                toggle.classList.toggle('bx-x')

                                                bodypd.classList.toggle('body-pd')

                                                headerpd.classList.toggle('body-pd')

                                                logoId.classList.toggle('logo_toggle')
                                        })
                                }
                        }

                        showNavbar('header-toggle', 'sidebar_logo', 'nav-bar', 'body-pdd', 'header')

                        const linkColor = document.querySelectorAll('.nav_link')

                        function colorLink() {
                                if (linkColor) {
                                        linkColor.forEach(l => l.classList.remove('activeMain'))
                                        this.classList.add('activeMain')

                                }
                        }

                        linkColor.forEach(l => l.addEventListener('click', colorLink))

                }
        </script>
</body>

</html>