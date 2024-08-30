<?php
$string = rawurlencode( $atts->text . ' ' . get_the_permalink( get_the_ID() ) . ' via @mooreandgiles' );
?>
<a class="blog-builder-module blog-builder-<?php echo $atts->slug; ?>" href="https://twitter.com/intent/tweet?text=<?php echo $string; ?>">
    <span class="blog-builder-<?php echo $atts->slug; ?>-content"><?php echo $atts->text; ?></span>
    <span class="blog-builder-<?php echo $atts->slug; ?>-icon">
        <svg width="130px" height="130px" viewBox="0 0 130 130" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <!-- Generator: Sketch 41.2 (35397) - http://www.bohemiancoding.com/sketch -->
            <title>twitter-badge</title>
            <desc>Created with Sketch.</desc>
            <defs></defs>
            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g id="twitter-badge">
                    <rect id="bg_box" fill="#EEEEEE" x="0" y="0" width="130" height="130" rx="8"></rect>
                    <path d="M106.9845,39.52325 C104.03375,40.839125 101.547,40.882625 98.911625,39.58125 C102.311875,37.544 102.46775,36.112125 103.696625,32.262375 C100.513875,34.151 96.98675,35.524875 93.234875,36.264375 C90.233375,33.067125 85.95225,31.0625 81.214375,31.0625 C72.115625,31.0625 64.742375,38.443 64.742375,47.5345 C64.742375,48.825 64.887375,50.082875 65.1665,51.29 C51.4785,50.604875 39.338375,44.04725 31.21475,34.0785 C29.797375,36.510875 28.985375,39.342 28.985375,42.361625 C28.985375,48.07825 31.89625,53.117 36.3115,56.071375 C33.610875,55.984375 31.073375,55.244875 28.854875,54.012375 C28.85125,54.08125 28.85125,54.1465 28.85125,54.219 C28.85125,62.20125 34.528,68.85675 42.068,70.372 C39.65375,71.028125 37.109,71.129625 34.625875,70.658375 C36.72475,77.197875 42.8075,81.961125 50.014,82.09525 C42.963375,87.61975 34.2235,89.921625 25.625,88.913875 C32.9185,93.5865 41.571375,96.3125 50.87675,96.3125 C81.1745,96.3125 97.74075,71.213 97.74075,49.444875 C97.74075,48.727125 97.729875,48.02025 97.69725,47.313375 C100.912625,44.993375 104.776875,42.82925 106.9845,39.52325 Z" id="twitter_bird"></path>
                </g>
            </g>
        </svg>
    </span>
</a>
