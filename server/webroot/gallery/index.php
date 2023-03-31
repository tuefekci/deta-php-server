<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Deta-PHPServer | Image Gallery Demo</title>
  <style>
    /* Basic CSS styling for the gallery */
    #gallery {
      display: flex;
      flex-wrap: wrap;
      justify-content: left;
      margin: 0 auto;
	  margin-top: 50px;
		margin-bottom: 50px;
	  max-width: 880px;
    }
    #gallery img {
      width: 200px;
      height: 200px;
      object-fit: cover;
      margin: 10px;
      cursor: pointer;
    }
    .lightbox {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .lightbox img {
      max-width: 70%;
      max-height: 70%;
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // jQuery code to show/hide the lightbox when an image is clicked
    $(document).ready(function() {
		$('.lightbox').fadeOut();

      $('.gallery-img').click(function() {
        var src = $(this).attr('src');
        var alt = $(this).attr('alt');
        $('#lightbox-img').attr('src', src);
        $('#lightbox-img').attr('alt', alt);
        $('.lightbox').fadeIn();
      });
      $('.lightbox').click(function() {
        $('.lightbox').fadeOut();
      });
    });
  </script>
</head>
<body>
  <div id="gallery">
    <?php
      $dir = 'images'; // Replace with the path to your folder
      $files = glob('./'.$dir.'/*.{jpg,jpeg,png,gif}', GLOB_BRACE); // Get all image files in the folder

      if (count($files) === 0) {
        echo 'No images found.';
      } else {
        foreach ($files as $file) {
          echo '<img class="gallery-img" src="' . $file . '" alt="' . basename($file) . '">';
        }
      }
    ?>
  </div>
  <div class="lightbox">
    <img id="lightbox-img">
  </div>
</body>
</html>
