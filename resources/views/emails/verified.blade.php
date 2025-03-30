<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
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
<div class="main-title">{{ $APP_NAME }}</div>

<div class="container">
    <div class="title-container">
            <span class="title">
                <slot>User Verified</slot>
                <span></span>
            </span>
    </div>

    <p>Hi <strong>{{ $user->username }}</strong>,</p>
    <p>Welcome to <a href="{{ $FRONTEND_URL }}">{{ $APP_NAME }}</a>! You are now verified!</p>

    <p>Thank you!</p>

</div>

<div class="footer">
    <p>&copy; <script>document.write(new Date().getFullYear());</script> {{ $APP_NAME }}. All rights reserved.</p>
</div>
</body>

</html>
