<?php

namespace ByebyeAI;

use ByebyeAI\Core;

delete_option( 'byebye_ai_enbled' );

// reset htaccess
Core\Core::instance()->apply_settings();

// kill options
delete_option( 'byebye_ai_htaccess' );
delete_option( 'byebye_ai_enbled' );
delete_option( 'byebye_ai_updated' );
