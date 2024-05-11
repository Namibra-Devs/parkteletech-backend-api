<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller {
    /**
    * Send a email.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function sendEmail( Request $request ) {
        // Validate the request data
        $request->validate( [
            'email' => 'required|email',
            'body' => 'required|string',
        ] );

        // Extract email and body from the request
        $receiverEmail = $request->input( 'email' );
        $body = $request->input( 'body' );

        try {
            // Send the email
            Mail::to( $receiverEmail )->send( new SendEmail( $body ) );

            // Email sent successfully
            return response()->json( [ 'message' => 'Email sent successfully' ], 200 );
        } catch ( \Exception $e ) {
            // Email sending failed
            echo $e;
            return response()->json( [ 'message' => 'Failed to send email' ], 500 );
        }
    }
}
