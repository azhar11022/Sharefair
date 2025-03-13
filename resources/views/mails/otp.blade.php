<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShareFair OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            background-color: #ffffff;
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .email-header img {
            width: 150px;
            height: auto;
        }
        .email-body {
            text-align: left;
            font-size: 16px;
            color: #333333;
            line-height: 1.6;
        }
        .otp-code {
            background-color: #f0f0f0;
            padding: 10px 20px;
            font-size: 22px;
            font-weight: bold;
            color: #333333;
            border-radius: 4px;
            display: inline-block;
            margin: 15px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #4CAF50;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 4px;
            margin-top: 20px;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Shair Fair</h1>
            {{-- <img src="{{ asset('images/sharefair-logo.png') }}" alt="ShareFair Logo"> --}}
        </div>

        <div class="email-body">
            <h2>OTP Verification for ShareFair</h2>
            <p>Hello {{ $name }},</p>
            <p>We received a request to verify your account. Please use the OTP code below to proceed:</p>

            <div class="otp-code">
                {{ $otp }}
            </div>

            <p>This OTP is valid for 10 minutes. If you didn't request this, you can ignore this email.</p>

            <p>If you need further assistance, feel free to contact us!</p>

            {{-- <a href="" class="cta-button">Verify Now</a> --}}
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ShareFair. All rights reserved.</p>
            <p>If you have any questions, feel free to contact our support team.</p>
        </div>
    </div>
</body>
</html>
