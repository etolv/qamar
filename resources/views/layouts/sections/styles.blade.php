<!-- BEGIN: Theme CSS-->
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet">
<style>
    .loading-spinner {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.5);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .spinner {
        width: 4rem;
        height: 4rem;
    }

    .card-size {
        min-height: calc(100vh - 182px);
        display: flex;
        flex-direction: column
    }

    .card-size .card-datatable {
        flex-grow: 2
    }

    .app-brand-logo {
        height: unset !important;
        width: unset !important;
    }

    .app-brand-logo svg {
        height: 40px !important;
        width: unset !important;
    }

    /* .layout-menu-fixed.layout-menu-hover .app-brand-logo svg, */
    .layout-menu-collapsed:not(.layout-menu-hover) .app-brand-logo svg {
        clip-path: inset(0 75% 0 0);
    }

    .rec-theme {
        position: absolute;
        width: 120px;
        height: 120px;
        border: 6px solid transparent;
        z-index: -1;
        border-radius: 30px;
        opacity: 0.5;
    }

    .rec1 {
        inset-inline: -40px auto;
        bottom: 50px;
        border-color: #2553af;
        width: 100px;
        height: 100px;
    }

    .rec2 {
        inset-inline: auto -40px;
        bottom: 4px;
        border-color: #2bd8cb;
    }

    .aside-circle {
        position: absolute;
        border-radius: 10000px;
        width: 200px;
        height: 200px;
        border: 5px solid;
        transition-duration: 0.7s;
        animation: float 10s infinite ease-in-out;

        &.circle1 {
            border-color: #4f008d;
            top: -54px;
            inset-inline-start: auto;
            inset-inline-end: -60px;
            width: 120px;
            height: 120px;
            opacity: 0.2;
            animation-delay: 0s;
            animation-duration: 12s;
        }

        &.circle2 {
            border-color: #2bd8cb;
            top: 50px;
            inset-inline-start: -30px;
            inset-inline-end: auto;
            height: 60px;
            width: 60px;
            opacity: 0.5;

            animation-delay: 1s;
            animation-duration: 15s;
        }

        &.circle3 {
            border-color: #2553af;
            bottom: 0px;
            inset-inline-start: auto;
            inset-inline-end: -130px;
            border-width: 10px;
            opacity: 0.5;

            animation-delay: 2s;
            animation-duration: 18s;
        }
    }

    @keyframes float {
        0% {
            transform: translate(0, 0);
        }

        25% {
            transform: translate(10px, -10px);
        }

        50% {
            transform: translate(-10px, 10px);
        }

        75% {
            transform: translate(10px, -10px);
        }

        100% {
            transform: translate(0, 0);
        }
    }

    .cericles-con {
        position: absolute;
        inset: 0;
        z-index: -2;
    }
</style>
@vite(['resources/assets/vendor/fonts/tabler-icons.scss', 'resources/assets/vendor/fonts/fontawesome.scss', 'resources/assets/vendor/fonts/flag-icons.scss'])
<!-- Core CSS -->
@vite(['resources/assets/vendor/scss' . $configData['rtlSupport'] . '/core' . ($configData['style'] !== 'light' ? '-' . $configData['style'] : '') . '.scss', 'resources/assets/vendor/scss' . $configData['rtlSupport'] . '/' . $configData['theme'] . ($configData['style'] !== 'light' ? '-' . $configData['style'] : '') . '.scss', 'resources/assets/css/demo.css'])

@vite(['resources/assets/vendor/libs/node-waves/node-waves.scss', 'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss', 'resources/assets/vendor/libs/typeahead-js/typeahead.scss'])

<!-- Vendor Styles -->
@yield('vendor-style')


<!-- Page Styles -->
@yield('page-style')
