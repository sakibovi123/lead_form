<?php
/*
    Plugin Name: Lead Form
    Plugin URI: http://example.com
    Description: Simple non-bloated WordPress Contact Form
    Version: 1.0
    Author: Mr.Sakib

*/

    // adding cutom css
    function bootstrap_css(){
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css');
    }


    function linking_jquery_cdn(){
        wp_enqueue_script(
            'ajax-script',
            'https://code.jquery.com/jquery-3.6.1.js',
            array( 'jquery' ),
            '1.0.0',
            true
        );

    }


    function activate_plugin_hook()
    {
        flush_rewrite_rules();
    }

    register_activation_hook( __FILE__, 'activate_plugin_hook' );



    function deactivate_plugin_hook()
    {
        flush_rewrite_rules();
    }

    register_deactivation_hook( __FILE__, 'deactivate_plugin_hook' );



    function linking_custom_js_script()
    {
        wp_enqueue_script(
            "post-script",
            plugins_url("/includes/lead_submit.js/"),
            array(), '1.0.1'
        );
    }



    function html_form_code() {

        echo '<div class="container bg-light p-4">';
        echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';

        echo 'Your First Name (required) <br />';
        echo '<input type="text" name="first_name" class="form-control">';
        echo '</p>';
        echo 'Your Last Name (required) <br />';
        echo '<input class="form-control" type="text" name="last_name" />';
        echo '</p>';
        echo '<p>';
        echo 'Your email <br />';
        echo '<input class="form-control" type="email" name="email" />';
        echo '</p>';
        echo '<p>';

        echo 'Phone<br />';
        echo '<input class="form-control" type="text" name="phone" />';
        echo '</p>';
        echo '<p>';

        echo '<p><input class="btn btn-success text-center" type="submit" name="lead-submitted" value="Send"/></p>';
        echo '</form>';
        echo '</div>';
    }

    function send_request_to_leadprosper(){
        $url = "https://api.leadprosper.io/ingest";

        if( isset( $_POST['lead-submitted'] ) ){
            $first_name = $_POST["first_name"];
            $last_name = $_POST["last_name"];
            $email = $_POST["email"];
            $phone = $_POST["phone"];

            $body = array(
               'lp_campaign_id' => '10056',
               'lp_supplier_id' => '21039',
               'lp_key' => 'xzmjar7ns7ppq',
               'first_name' => $first_name,
               'last_name' => $last_name,
               'email' => $email,
               'phone' => $phone,
               'lp_action' => 'test',
           );

            print_r($body);

            $args = array(
                'body'        => $body,
                'timeout'     => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array(),
                'cookies'     => array(),
            );



            $body = json_encode( $body );

            $response = wp_remote_post($url, $args);

            if( $response["response"]["code"] == 200 ){

                // sending request to zapier

                $zap_body = array(

                    "first_name"=> $first_name,
                    "last_name" => $last_name,
                    "phone" => "$phone",
                    "email" => "$email"
                );
             $zap_args = array(
                'body'        => $zap_body,
                'timeout'     => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array(),
                'cookies'     => array(),
            );

                $zap_body = json_encode( $zap_body );
                $webhook_url = "";
                $send_request = wp_remote_post( $webhook_url, $zap_args );

                echo "SUCCESS";
            }
            else{
                echo "FAILED TO SUBMIT LEAD";
            }

        }

    }


    function generate_short_code() {
        ob_start();
        send_request_to_leadprosper();
        html_form_code();

        return ob_get_clean();
    }


    add_shortcode( 'lead-form', 'generate_short_code' );


// all actions goes here

    add_action("init", "bootstrap_css");
    add_action("init", "linking_jquery_cdn");
    add_action("init", "linking_custom_js_script");

?>