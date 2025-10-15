<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Qamar samaya</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link type="text/css" rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/fontawesome.min.css" media="all" />
    <link type="text/css" rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/brands.min.css" media="all" />
    <link type="text/css" rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/solid.min.css" media="all" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/regular.min.css"
        integrity="sha512-TzeemgHmrSO404wTLeBd76DmPp5TjWY/f2SyZC6/3LsutDYMVYfOx2uh894kr0j9UM6x39LFHKTeLn99iz378A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/home-style.css" />
</head>

<body>
    <header>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg">
                        <div class="container-fluid">
                            <a class="navbar-brand" href="#">
                                <img src="assets/imgs/logo/logo.svg" alt="" />
                            </a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar"
                                aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                                aria-labelledby="offcanvasNavbarLabel">
                                <div class="offcanvas-header">
                                    <img src="assets/imgs/logo/logo.svg" alt="" />
                                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <ul class="navbar-nav justify-content-center flex-grow-1">
                                        <li class="nav-item">
                                            <a class="nav-link active" aria-current="page" href="#home">Home</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#about-us">About Us</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#download">Download App</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#specs">Benefits</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#faq">F.A.Q</a>
                                        </li>
                                    </ul>
                                    <ul class="navbar-nav justify-content-end right-nav">
                                        <li class="nav-item language language-switcher">
                                            <a class="nav-link" href="{{ route('swap', 'ar') }}">AR</a>
                                        </li>
                                        <!-- <li class="nav-item language">
                                                <a class="nav-link" href="#download">Get Started</a>
                                            </li> -->
                                    </ul>
                                    <div class="footer-icon d-flex justify-content-center">
                                        <a href="https://www.facebook.com/qamar.samaya.5">
                                            <i class="fa-brands fa-facebook-f"></i>
                                        </a>
                                        <a href="https://www.instagram.com/qamarsamaya">
                                            <i class="fa-brands fa-instagram"></i>
                                        </a>
                                        <a href="https://www.youtube.com/@qamarsamaya">
                                            <i class="fa-brands fa-youtube"></i>
                                        </a>
                                        <a href="https://wa.me/966551114415">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    </div>
                                </div>

                                <img src="{{ asset('assets/imgs/logo/logo.svg') }}" alt="" />
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <main id="home">
        <div class="banner-container">
            <div class="banner">
                <div class="type-container">
                    <div class="container">
                        <div class="mb-5 d-flex flex-column align-items-center gap-3 text-center">
                            <div class="">
                                <div class="" data-aos="fade-down" data-aos-duration="1500">
                                    Qamar Samaya<br />Your <span class="orange">beauty</span>,
                                    your <span class="orange">way</span>.
                                </div>
                            </div>

                            <div class="">
                                <img data-aos="fade-up" data-aos-duration="1500" class="banner-image"
                                    src="assets/imgs/logo/logo-light.svg" alt="" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="devider case-slider">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="#000">
                        <path d="M500 80.7C358 68 0 4 0 4V0h1000v84.7c-216 23.3-358 8.6-500-4Z" opacity=".3"></path>
                        <path d="M500 65.7C358 53 0 4 0 4V0h1000v62.7c-216 23.3-358 15.6-500 3Z" opacity=".5"></path>
                        <path d="M500 50.7C358 38 0 4 0 4V0h1000v40.7C784 64 642 63.3 500 50.7Z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <section id="about-us" class="section section-0 light-bg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 about">
                        <h4>Who are we?</h4>
                        <p>
                            Welcome to Qamar samaya, your ultimate platform for all things
                            makeup, beauty, and skin care. Our mission is to help you look
                            and feel your best by providing expert advice, tutorials, and
                            product recommendations.
                        </p>
                        <p>
                            At Qamar samaya, we believe in empowering individuals to embrace
                            their unique beauty. Whether you're a makeup enthusiast, a
                            skincare newbie, or just looking for tips to enhance your
                            natural glow, we've got you covered. Our app features
                            comprehensive guides, step-by-step tutorials, and personalized
                            recommendations to make your beauty journey enjoyable and
                            effective.
                        </p>
                        <p>
                            Join the Qamar samaya community and explore a world of beauty
                            tips and tricks at your fingertips. Discover new products,
                            master makeup techniques, and achieve flawless skin with ease.
                            Your beauty, your way.
                        </p>
                    </div>

                    <div class="col-lg-6 d-flex">
                        <img src="assets/imgs/mobile2.jpg" alt="" />
                    </div>
                </div>
            </div>
        </section>

        <section id="download" class="section section7 gray-1">
            <div class="devider case9">
                <svg id="Group_1" data-name="Group 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 33">
                    <path id="Path_1" data-name="Path 1"
                        d="M0,0V33c250,0,375-7.92,500-15.84C625,25.08,750,33,1000,33V0Z" opacity="0.5" />
                    <path id="Path_2" data-name="Path 2"
                        d="M0,0V1.231C250,1.231,375,8.615,500,16,625,8.615,750,1.231,1000,1.231V0Z" />
                </svg>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mobile-download-content">
                            <div class="title">
                                <span class="orange">Download</span> <span>the App</span>
                            </div>
                            <div class="content">
                                Explore a world of knowledge and education by downloading
                                Qamar samaya, available on all platforms.
                            </div>
                            <div class="appdownload">
                                <div class="flex social-btns">
                                    <a class="app-btn blu flex vert" href="http://apple.com">
                                        <i class="fab fa-apple"></i>
                                        <p>
                                            Available on the <br />
                                            <span class="big-txt">App Store</span>
                                        </p>
                                    </a>

                                    <a class="app-btn blu flex vert" href="http://google.com">
                                        <i class="fab fa-google-play"></i>
                                        <p>
                                            Get it on <br />
                                            <span class="big-txt">Google Play</span>
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img src="assets/imgs/finder.png" alt="" />
                    </div>
                </div>
            </div>

            <div class="devider case1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="#000">
                    <path
                        d="M0 0v99.7C62 69 122.4 48.7 205 66c83.8 17.6 160.5 20.4 240-12 54-22 110-26 173-10a392.2 392.2 0 0 0 222-5c55-17 110.3-36.9 160-27.2V0H0Z"
                        opacity=".5"></path>
                    <path
                        d="M0 0v74.7C62 44 122.4 28.7 205 46c83.8 17.6 160.5 25.4 240-7 54-22 110-21 173-5 76.5 19.4 146.5 23.3 222 0 55-17 110.3-31.9 160-22.2V0H0Z">
                    </path>
                </svg>
            </div>
        </section>

        <div id="specs" class="section section-0-2 specs">
            <div class="pt-3 pb-5 text-center">
                <h2 class="has-color">
                    <b>App <span>Features</span></b>
                </h2>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="per-spec">
                            <img src="assets/imgs/tick.svg" alt="" />
                            <div class="psc">
                                <div class="title">Personalized Beauty Tips</div>
                                <p>
                                    Receive personalized beauty and skin care tips tailored to
                                    your needs. Achieve your best look with expert advice.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="per-spec">
                            <img src="assets/imgs/tick.svg" alt="" />
                            <div class="psc">
                                <div class="title">Expert Tutorials</div>
                                <p>
                                    Access step-by-step tutorials from beauty experts. Learn
                                    makeup techniques and skincare routines from the best in the
                                    industry.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="per-spec">
                            <img src="assets/imgs/tick.svg" alt="" />
                            <div class="psc">
                                <div class="title">Product Recommendations</div>
                                <p>
                                    Get product recommendations that suit your skin type and
                                    beauty needs. Find the best products to enhance your beauty
                                    routine.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="per-spec">
                            <img src="assets/imgs/tick.svg" alt="" />
                            <div class="psc">
                                <div class="title">Community Engagement</div>
                                <p>
                                    Join a vibrant community of beauty enthusiasts. Share your
                                    tips, get feedback, and connect with others who share your
                                    passion.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="per-spec">
                            <img src="assets/imgs/tick.svg" alt="" />
                            <div class="psc">
                                <div class="title">Virtual Consultations</div>
                                <p>
                                    Book virtual consultations with top beauty and skincare
                                    experts. Get professional advice without leaving your home.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="devider case2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="#000">
                <path d="M500 80.7C358 68 0 4 0 4V0h1000v84.7c-216 23.3-358 8.6-500-4Z" opacity=".3"></path>
                <path d="M500 65.7C358 53 0 4 0 4V0h1000v62.7c-216 23.3-358 15.6-500 3Z" opacity=".5"></path>
                <path d="M500 50.7C358 38 0 4 0 4V0h1000v40.7C784 64 642 63.3 500 50.7Z"></path>
            </svg>
        </div>

        <section id="faq" class="section section-faq section1">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h4>Frequently Asked Questions</h4>
                    </div>
                    <div class="col-lg-12">
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                                        aria-controls="collapseOne">
                                        What is Qamar Samaya?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        Qamar Samaya is a comprehensive platform dedicated to
                                        beauty, makeup, and skin care. We connect users with
                                        expert advice, tutorials, and product recommendations to
                                        help them look and feel their best.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true"
                                        aria-controls="collapseTwo">
                                        How do I book a virtual consultation?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        Booking a virtual consultation is easy. Simply download
                                        the Qamar Samaya app, select your preferred beauty expert,
                                        and choose a convenient time for your session. You can
                                        also book through our website by logging into your
                                        account.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                        aria-expanded="true" aria-controls="collapseThree">
                                        What services does Qamar Samaya offer?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        Qamar Samaya offers a wide range of services including
                                        makeup tutorials, skincare advice, product
                                        recommendations, and virtual consultations with beauty
                                        experts. Our goal is to provide you with all the tools and
                                        knowledge you need to enhance your beauty routine.
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="true"
                                        aria-controls="collapseFour">
                                        How can I join the Qamar Samaya community?
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        Joining the Qamar Samaya community is simple. Download our
                                        app and sign up to connect with beauty enthusiasts and
                                        experts. Share your tips, get feedback, and stay updated
                                        on the latest beauty trends and products.
                                    </div>
                                </div>
                            </div> --}}

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="true"
                                        aria-controls="collapseFive">
                                        Can I get personalized beauty tips?
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        Yes, Qamar Samaya provides personalized beauty and
                                        skincare tips tailored to your individual needs. Simply
                                        fill out a brief questionnaire in the app, and our experts
                                        will provide recommendations based on your unique profile.
                                    </div>
                                </div>
                            </div>

                            <!-- Add more questions as needed -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="devider case1 dark">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="#000">
            <path
                d="M0 0v99.7C62 69 122.4 48.7 205 66c83.8 17.6 160.5 20.4 240-12 54-22 110-26 173-10a392.2 392.2 0 0 0 222-5c55-17 110.3-36.9 160-27.2V0H0Z"
                opacity=".5"></path>
            <path
                d="M0 0v74.7C62 44 122.4 28.7 205 46c83.8 17.6 160.5 25.4 240-7 54-22 110-21 173-5 76.5 19.4 146.5 23.3 222 0 55-17 110.3-31.9 160-22.2V0H0Z">
            </path>
        </svg>
    </div>
    <footer id="footer" class="footer gray-1">
        <div class="container">
            <div class="row">
                <a class="logo d-lg-none d-flex" href="#">
                    <img src="assets/imgs/logo/logo-light.svg" class="logo-light" alt=" " />
                </a>
                <div class="col-lg-6">
                    <h5>Useful Links</h5>
                    <ul class="list-inline footer-link mb-0">
                        <li class="list-item">
                            <a class="newLinkHover" href="{{ route('privacy_policy') }}">Privacy Policy</a>
                        </li>
                        <li class="list-item">
                            <a class="newLinkHover" href="{{ route('terms_conditions') }}">Terms and conditions</a>
                        </li>
                        <!-- <li class="list-item ">
                                <a class="newLinkHover" href="#contact ">Lorem ipsum dollr</a>
                            </li>
                            <li class="list-item ">
                                <a class="newLinkHover" href="#contact ">Lorem ipsum dollr</a>
                            </li> -->
                    </ul>
                </div>
                <!-- <div class="col-lg-4 col-sm-6">
                        <h5>SERVICES</h5>
                        <ul class="list-inline footer-link mb-0 ">
                            <li class="list-item ">
                                <a class="newLinkHover" href="#contact ">Lorem ipsum dollr</a>
                            </li>
                            <li class="list-item ">
                                <a class="newLinkHover" href="#contact ">Lorem ipsum dollr</a>
                            </li>
                            <li class="list-item ">
                                <a class="newLinkHover" href="#contact ">Lorem ipsum dollr</a>
                            </li>
                            <li class="list-item ">
                                <a class="newLinkHover" href="#contact ">Lorem ipsum dollr</a>
                            </li>
                        </ul>
                    </div> -->
                <div class="col-lg-6">
                    <h5>FIND US</h5>
                    <ul class="buttns list-inline footer-link mb-0">
                        <li class="list-item">
                            <a class="btn d-flex" href="https://maps.app.goo.gl/uxYyGZ7tV9Dso3c89?g_st=iw " target="_blank">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <div>
                                        <span>Get Direction</span>
                                        <p>Salman Al Farsi, Riyadh</p>
                                    </div>
                                </div>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </li>
                        <li class="list-item">
                            <a class="btn d-flex" href="tel:+966551114415" target="_blank">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="bi bi-telephone-fill"></i>
                                    <div>
                                        <span>Call Us</span>
                                        <p>+966 55 111 4415</p>
                                    </div>
                                </div>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </li>
                        <li class="list-item">
                            <a class="btn d-flex" href="mailto:qamarsamaya@gmail.com" target="_blank">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="bi bi-envelope-fill"></i>
                                    <div>
                                        <span>Email</span>
                                        <p>qamarsamaya@gmail.com</p>
                                    </div>
                                </div>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-12">
                    <div class="footer-icon d-flex justify-content-center">
                        <a href="https://www.facebook.com/qamar.samaya.5">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/qamarsamaya">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://www.youtube.com/@qamarsamaya">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                        <a href="https://wa.me/966551114415">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-12 text-center">
                    <hr />
                    <span>All rights reserved @ Qamar Samaya</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-parallax-js@5.5.1/dist/simpleParallax.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="assets/js/main.js"></script>
</body>

</html>
