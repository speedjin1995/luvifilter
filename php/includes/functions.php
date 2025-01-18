<?php
function custom_register_user( $request ) {
    // Get parameters from the request
    $username = sanitize_text_field( $request->get_param( 'username' ) );
    $email = sanitize_email( $request->get_param( 'email' ) );
    $password = $request->get_param( 'password' );

    // Validate required fields
    if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
        return new WP_Error( 'missing_fields', 'Please provide username, email, and password.', array( 'status' => 400 ) );
    }

    // Check if the username or email already exists
    if ( username_exists( $username ) || email_exists( $email ) ) {
        return new WP_Error( 'user_exists', 'Username or email already exists.', array( 'status' => 400 ) );
    }

    // Create the user
    $user_id = wp_create_user( $username, $password, $email );

    if ( is_wp_error( $user_id ) ) {
        return new WP_Error( 'user_creation_failed', 'Failed to create user.', array( 'status' => 500 ) );
    }

    // Set default role (optional)
    $user = new WP_User( $user_id );
    $user->set_role( 'subscriber' );

    // Return success response
    return rest_ensure_response( [
        'id'       => $user_id,
        'username' => $username,
        'email'    => $email,
    ] );
}

function register_custom_user_endpoint() {
    register_rest_route( 'custom/v1', '/register', array(
        'methods'  => 'POST',
        'callback' => 'custom_register_user',
        'args'     => array(
            'username' => array( 'required' => true ),
            'email'    => array( 'required' => true ),
            'password' => array( 'required' => true ),
        ),
    ) );
}

add_action( 'rest_api_init', 'register_custom_user_endpoint' );
?>