<script>
  $(document).ready(function () {
    $('.section-1').addClass('retailSectionAnimate');
    $('.section-2').addClass('retailSectionAnimate');
    


    // Smooth scrolling
    window.addEventListener('scroll', function() {
      if(window.scrollY > 150) { 
          document.querySelector('#navbar').style.transition = 'all 1s'; 
          document.querySelector('#navbar').style.backgroundColor = '#f9f9f9'; 
          document.querySelector('#navbar').style.borderBottom = '#f9f9f9 1px solid'; 
          document.querySelector('#navbar .nav-link').style.backgroundColor = '#f9f9f9'; 
      } else {
          document.querySelector('#navbar').style.borderBottom = '#fff';
          document.querySelector('#navbar').style.backgroundColor = '#fff';
          document.querySelector('#navbar .nav-link').style.backgroundColor = '#fff'; 
      }
    });

    $('#sidebar #homeAnchor, #sidebar #contactAnchor, #navbar a, .about-btn, .features-btn, .breadcrumb a, #myBtn').on('click', function (e) {
      if (this.hash !== '') {
        e.preventDefault();

        const hash = this.hash;

        $('html, body').animate(
          {
            scrollTop: $(hash).offset().top - 100,
          },
          800
        );
      }
    });

    // Animation Effects
    $(window).scroll(function () {
      let position = $(this).scrollTop();
      
      if (position >= 159) {
        $('#showcaseHighlights').addClass('customShowcase');
      }

      if (position >= 852) {
        $('.cardLeft').addClass('animateCardLeft');
        $('.cardRight').addClass('animateCardRight');
      }

      if (position >= 2300) {
        $('.contact-form').addClass('animateContactLeft');
        $('.contact-image').addClass('animateContactRight');
      }

      if (position >= 1715) {
        $('.featureLeft').addClass('animateFeatureLeft');
        $('.featureRight').addClass('animateFeatureRight');
      }
    });
  });
</script>