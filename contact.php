<link rel="stylesheet" href="contact.css">
<?php include 'Pages/header.php'; ?>

<div class="page-navigation-wrapper">
    <div class="book1">
        <img src="/Booksy/AssetS/shop-default/book1.png" alt="book">
    </div>
    <div class="book2">
        <img src="/Booksy/AssetS/shop-default/book2.png" alt="book">
    </div>
    <div class="container">
        <div class="page-heading">
            <h1>Contact Us</h1>
            <div class="page-header">
                <ul class="custom-navigation">
                    <li><a href="/Booksy/index.php">Home</a></li>
                    <li><i class="bi bi-chevron-right"></i></li>
                    <li>Contact Us</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<section class="contact-section fix section-padding">
    <div class="container" style="max-width: 1595px;">
        <div class="contact-wrapper-new">
            <div class="row g-4 align-items-center">
                <div class="col-lg-4">
                    <div class="contact-left-new">
                        <div class="contact-details">
                            <div class="contact-item mb-4">
                                <div class="icon-box">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <div class="text-content">
                                    <p>Call Us 7/24</p>
                                    <h3>
                                        <a href="tel:+2085550112">+208-555-0112</a>
                                    </h3>
                                </div>
                            </div>
                            <div class="contact-item mb-4">
                                <div class="icon-box">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <div class="text-content">
                                    <p>Make a Quote</p>
                                    <h3>
                                        <a href="mailto:example@gmail.com">example@gmail.com</a>
                                    </h3>
                                </div>
                            </div>
                            <div class="contact-item border-none">
                                <div class="icon-box">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div class="text-content">
                                    <p>Location</p>
                                    <h3>
                                        4517 Washington Ave.
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="media-box">
                            <img src="/Booksy/Assets/contact/contact.jpg" alt="Contact Image">
                            <div class="video-overlay">
                                <a href="https://www.youtube.com/watch?v=Cn4G2lZ_g2I" class="video-play-button ripple video-popup">
                                    <i class="bi bi-play-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="contact-form-container">
                        <h2>Ready to Get Started?</h2>
                        <p>
                        Now the course of the lecture is always. Aenean has a responsibility to the arch to ease the consequences. Among and the hunger of the family before the first in the mouth. It is not the elite, but the just increases. The authority was there, not the edge of the time. Aenean dignity.
                        </p>
                        <form action="contact.php" id="contact-form" method="POST" class="form-wrapper">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="input-field">
                                        <span>Your Name*</span>
                                        <input type="text" name="name" id="name" placeholder="Your Name">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-field">
                                        <span>Your Email*</span>
                                        <input type="text" name="email" id="email123" placeholder="Your Email">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-field">
                                        <span>Write Message*</span>
                                        <textarea name="message" id="message" placeholder="Write Message"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <button type="submit" class="submit-btn">
                                        Send Message <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="map-section">
    <div class="map-items">
        <div class="googpemap">
            <iframe
                src="https://www.google.com/maps?q=21.234252,72.864299&hl=en&z=15&output=embed"
                style="border:0;" allowfullscreen="" loading="lazy">
            </iframe>
        </div>
    </div>
</div>
<?php include 'Pages/footer.php'; ?>