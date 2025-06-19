<?php
include 'header.php';
?>

  <!--================Home Banner Area =================-->
  <!-- breadcrumb start-->
  <section class="breadcrumb breadcrumb_bg">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="breadcrumb_iner">
            <div class="breadcrumb_iner_item">
              <h2>contact us</h2>
              <p>Home <span>-</span> contact us</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- breadcrumb start-->

  <!-- ================ contact section start ================= -->
  <section class="contact-section padding_top">
    <div class="container">
      <div class="d-none d-sm-block mb-5 pb-4">
        <div id="map" style="height: 480px;"></div>
        <script>
          function initMap() {
            var uluru = {
              lat: 28.6139,
              lng: 77.2090
            };
            var grayStyles = [{
                featureType: "all",
                stylers: [{
                    saturation: -90
                  },
                  {
                    lightness: 50
                  }
                ]
              },
              {
                elementType: 'labels.text.fill',
                stylers: [{
                  color: '#ccdee9'
                }]
              }
            ];
            var map = new google.maps.Map(document.getElementById('map'), {
              center: {
                lat: 28.6139,
                lng: 77.2090
              },
              zoom: 12,
              styles: grayStyles,
              scrollwheel: false
            });
            
            // Add marker for Delhi location
            new google.maps.Marker({
              position: uluru,
              map: map,
              title: 'Our Office'
            });
          }
        </script>
        <script
          src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDpfS1oRGreGSBU5HHjMmQ3o5NLw7VdJ6I&callback=initMap">
        </script>
      </div>

      <div class="row">
        <div class="col-12 text-center mb-5">
          <h2 class="contact-title">Get in Touch</h2>
          <p class="lead">We'd love to hear from you! Reach out for inquiries, support, or just to say hello.</p>
        </div>
        
        <div class="col-lg-8">
          <form class="form-contact contact_form" action="contact_process.php" method="post" id="contactForm" novalidate="novalidate">
            <div class="row">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="message" class="form-label">Your Message</label>
                  <textarea class="form-control w-100" name="message" id="message" cols="30" rows="6"
                    placeholder='Enter your message here...'></textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-3">
                  <label for="name" class="form-label">Full Name</label>
                  <input class="form-control" name="name" id="name" type="text" 
                    placeholder='Enter your full name'>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-3">
                  <label for="email" class="form-label">Email Address</label>
                  <input class="form-control" name="email" id="email" type="email" 
                    placeholder='Enter your email address'>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="subject" class="form-label">Subject</label>
                  <input class="form-control" name="subject" id="subject" type="text" 
                    placeholder='Enter subject'>
                </div>
              </div>
            </div>
            <div class="form-group mt-3">
              <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
            </div>
          </form>
        </div>
        
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <div class="media contact-info">
                <span class="contact-info__icon"><i class="ti-home"></i></span>
                <div class="media-body">
                  <h4>New Delhi Office</h4>
                  <p>Connaught Place, Block C<br>New Delhi 110001<br>India</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <div class="media contact-info">
                <span class="contact-info__icon"><i class="ti-tablet"></i></span>
                <div class="media-body">
                  <h4>Contact Numbers</h4>
                  <p>+91 11 2345 6789 (Office)<br>+91 98765 43210 (Mobile)<br>Mon-Sat: 9:30 AM - 6:30 PM</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="media contact-info">
                <span class="contact-info__icon"><i class="ti-email"></i></span>
                <div class="media-body">
                  <h4>Email Addresses</h4>
                  <p>info@example.com (General)<br>support@example.com (Support)<br>careers@example.com (Careers)</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ================ contact section end ================= -->
<?php
include 'footer.php';
?>