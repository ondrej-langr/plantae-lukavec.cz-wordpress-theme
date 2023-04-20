    <?php if (get_post_type() == "post"): ?>
      </article>
    <?php endif; ?>
    <?php if (is_front_page()): ?>
    </main>
    <?php endif; ?>
    <footer scope="site">
      <div class="content-wrap">
        <div class="inner-wrap">
          <div class="mapouter">
            <div class="gmap_canvas">
              <iframe width="450" height="292" id="gmap_canvas" src="https://maps.google.com/maps?q=Lukavec%20u%20ho%C5%99ic%2013&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
            </div>
            <style>
              .mapouter{position:relative;text-align:right;height:100%;width:450px;}
              .gmap_canvas {overflow:hidden;background:none!important;    height: 100%;width: 100%;}
            </style>
            </div>
            <div class="address">
              <address class="">
                <span class="name">Michal Jon</span><br>
                <span>Lukavec u Hořic 13</span><br>
                <span>Hořice v Podkrkonoší</span><br>
                <span>50801</span><br>
                <hr>
                <span class="s">IČO: 69148007</span><br>
                <span class="s">Č.Ú.:8825027001/5500</span><br>
                <span class="s">IBAN: CZ56 5500 0000 0088 2502 7001</span><br>
              </address>
            </div>
            <div class="links">
              <ul>
                <li>
                  <a href="tel:606651356">
                    <i class="fas fa-phone" aria-hidden></i> <span>606 651 356</span>
                  </a>
                </li>
                <li>
                  <a href="mailto:jon.m@seznam.cz?subject=Zpráva">
                    <i class="fas fa-envelope" aria-hidden></i> <span>jon.m@seznam.cz</span>
                  </a>
                </li>
                <li>
                  <a href="https://www.facebook.com/rostlinyprodej" target="_blank">
                    <i class="fab fa-facebook-f" aria-hidden></i> <span>Facebook</span>
                  </a>
                </li>
                <li>
                  <a href="https://www.instagram.com/okrasneuzitkoverostliny/?hl=cs" target="_blank">
                    <i class="fab fa-instagram" aria-hidden></i> <span>Instagram</span>
                  </a>
                </li>
              </ul>
            </div>
        </div>
      </div>
    </footer>
    <div class="" style="height: 30px; width: 100%; display: flex; align-items:center; justify-content: center; background: white;">
      <p>&copy; 2019-<?php echo date("Y"); ?> <a href="<?php echo home_url("/"); ?>">plantae-lukavec.cz</a></p>
    </div>
    <!-- Wordpress footer Start-->
      <?php wp_footer(); ?>
    <!-- Wordpress footer End-->
  </body>
</html>
