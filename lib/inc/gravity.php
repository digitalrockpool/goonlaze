<?php

/* Includes: Gravity Forms

@package	Goonlaze
@author		Digital Rockpool
@link		https://goonlaze.uk
@copyright	Copyright (c) 2018, Digital Rockpool LTD
@license	GPL-2.0+ */


// READ ONLY FIELD
add_filter('gform_pre_render', 'add_readonly_script');
function add_readonly_script($form) { ?>
    
  <script type="text/javascript">
    jQuery(document).ready(function() {
      jQuery(".gf_readonly input").attr("readonly","readonly");
    } );
  </script> <?php
  
  return $form;
}