$('.breaking-news button').click(function () {
  $('.breaking-news').hide();
});

$(document).ready(function () {
  $(window).scroll(function () {
    if ($(document).scrollTop() > 200) {
      $('header').addClass('fixed');
    } else {
      $('header').removeClass('fixed');
    }
  });
});

/////////////////////////////////

document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', function () {
    var offcanvasElement = document.getElementById('offcanvasNavbar');
    if (offcanvasElement) {
      // Check if the element exists
      var bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
      if (bsOffcanvas) {
        bsOffcanvas.hide();
      }
    }
  });
});

/////////////////////////////////

const parallexed_2El = document.querySelector('.banner-main-image.desk img');
if (parallexed_2El) {
  var parallexed_2 = document.querySelector('.banner-main-image.desk img');
  new simpleParallax(parallexed_2, {
    delay: 0.7,
    scale: 1.5,
    overflow: true,
    transition: 'cubic-bezier(.3,.95,.3,.95)'
  });
}

/////////////////////////////////

window.addEventListener('scroll', function () {
  var element = document.getElementById('back-to-top');
  if (element) {
    if (window.scrollY >= 200) {
      element.classList.add('scrolled');
    } else {
      element.classList.remove('scrolled');
    }
  }
});

$('#back-to-top').click(function () {
  $('body, html').animate({ scrollTop: 0 });
});

//////////////////////////////
$(document).on('click', 'a[href^="#"]', function (e) {
  var id = $(this).attr('href');
  // Ensure the id is a valid selector
  var $id;
  try {
    $id = $(id);
  } catch (error) {
    return;
  }

  if ($id.length === 0) {
    return;
  }
  e.preventDefault();
  var pos = $id.offset().top - 56;
  $('body, html').animate({ scrollTop: pos }, 800);
});

// $(window).scroll(function () {
//   var scrollPos = $(this).scrollTop();
//   $("nav li a").each(function () {
//     var href = $(this).attr("href");
//     if (href) {
//       // Remove the leading slash and hash to use as a selector
//       var targetId = href.replace(/^[#/]+/, "");
//       var target = document.getElementById(targetId);
//       if (target) {
//         // Ensure the target exists
//         var targetTop = $(target).offset().top; // Get the offset top of the target element
//         if (scrollPos >= targetTop - 102) {
//           $("nav li a.active").removeClass("active");
//           $(this).addClass("active");
//         }
//       }
//     }
//   });
// });
$(window).scroll(function () {
  var scrollPos = $(this).scrollTop(); // Get the current scroll position

  $('nav li a').each(function () {
    var href = $(this).attr('href'); // Get the href attribute

    // Check if href is valid, not empty, not '#', and starts with '#'
    if (href && href.startsWith('#') && href.length > 1) {
      var target = $(href); // Get the target element using href as a selector

      if (target.length) {
        // Ensure the target exists
        if (scrollPos >= target.offset().top - 102) {
          $('nav li a.active').removeClass('active');
          $(this).addClass('active');
        }
      }
    }
  });
});

//////////////////////////////

$(document).ready(function () {
  $('.subcategories > div > div:first-child span.category').addClass('active');
  $('span.category').click(function () {
    if ($(this).attr('data-cat') == 'all') {
      $('.filtered-item').show();
      $('span.category').removeClass('active');
      $(this).addClass('active');
    } else {
      filter_val = $(this).attr('data-cat');
      $('.filtered-item').hide().removeClass('active');
      $('.' + filter_val + '')
        .show()
        .addClass('active');
      $('span.category').removeClass('active');
      $(this).addClass('active');
    }
  });
});

///////////////////////////////

var testimonials = new Swiper('.testimonials', {
  slidesPerView: 1,
  spaceBetween: 0,
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev'
  },
  pagination: {
    el: '.swiper-pagination',
    clickable: true
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const sparklesContainer = document.createElement('div');
  sparklesContainer.classList.add('sparkles');
  const typeContainer = document.querySelector('div.type-container');
  if (typeContainer) {
    typeContainer.appendChild(sparklesContainer);

    for (let i = 0; i < 50; i++) {
      const sparkle = document.createElement('div');
      sparkle.classList.add('sparkle');
      sparkle.style.left = `${Math.random() * 100}vw`;
      sparkle.style.top = `${Math.random() * 100}vh`;
      sparkle.style.animationDelay = `${Math.random() * 2}s`;
      sparklesContainer.appendChild(sparkle);
    }
  }
});
