<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
{{--    <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}" />--}}
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .main-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .title-container {
            margin: 20px 0;
            text-align: center;
        }

        .title {
            font-size: 2rem;
            font-weight: bold;
            text-transform: uppercase;
            position: relative;
            display: inline-block;
        }

        .title span {
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, #f44336, #ff9800, #ffc107);
            border-radius: 5px;
        }

        p {
            color: #555555;
            line-height: 1.5;
            font-size: 16px;
        }

        .button {
            display: block;
            margin: 20px auto;
            color: white;
            font-weight: bold;
            padding: 12px 24px;
            font-size: 1rem;
            border-radius: 8px;
            background: linear-gradient(to right, #3b82f6, #1e40af);
            box-shadow: 0px 4px 10px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease-in-out;
            border: none;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            width: fit-content;
        }

        .button:hover {
            background: linear-gradient(to bottom, #3b82f6, #1e40af);
            box-shadow: 0px 6px 15px rgba(59, 130, 246, 0.5);
            transform: scale(1.05);
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777777;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            .title {
                font-size: 1.5rem;
            }

            h1 {
                font-size: 24px;
            }

            .button {
                box-shadow: 0px 4px 10px rgba(30, 58, 138, 0.7);
            }
        }
    </style>
</head>

<body>
<div class="main-title">Repository of Research Works</div>

<div class="container">
    <div class="title-container">
            <span class="title">
                <slot>Verify your email address</slot>
                <span></span>
            </span>
    </div>

    <p>Hi <strong>{{ $user->username }}</strong>,</p>
    <p>Thank you for registering to the <a href="{{ $FRONTEND_URL }}">Repository of Research Works</a>! Please click the button below to verify your email address:</p>

    <a href="{{ $url }}" class="button">Verify Email Address</a>

    <p>If you did not create an account, no further action is required.</p>

    <p>Thank you!</p>

    <hr>
    <div class="footer">
        If you're having trouble clicking the "Verify Email Address" button, copy and paste the following URL into your web browser:
        <a href="{{ $url }}">{{ $url }}</a>
    </div>
</div>

<div class="footer">
    <p>&copy; <script>document.write(new Date().getFullYear());</script> Repository of Research Works. All rights reserved.</p>
</div>
</body>

</html>
