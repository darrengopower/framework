<!doctype html>
<html>
<head>
    <title>欢迎使用 Notadd</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
    <style>
        body {
            background: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        body, input, button {
            font-family: 'Open Sans', sans-serif;
            font-size: 16px;
            color: #7E96B3;
        }
        .container {
            max-width: 515px;
            margin: 0 auto;
            padding: 100px 30px;
            text-align: center;
        }
        a {
            color: #e7652e;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        h1 {
            margin-bottom: 40px;
        }
        h2 {
            font-size: 28px;
            font-weight: normal;
            color: #3C5675;
            margin-bottom: 0;
        }
        form {
            margin-top: 40px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group .form-field:first-child input {
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }
        .form-group .form-field:last-child input {
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        .form-field input {
            background: #EDF2F7;
            margin: 0 0 1px;
            border: 2px solid transparent;
            transition: background 0.2s, border-color 0.2s, color 0.2s;
            width: 100%;
            padding: 15px 15px 15px 180px;
            box-sizing: border-box;
        }
        .form-field input:focus {
            border-color: #e7652e;
            background: #fff;
            color: #444;
            outline: none;
        }
        .form-field label {
            float: left;
            width: 160px;
            text-align: right;
            margin-right: -160px;
            position: relative;
            margin-top: 18px;
            font-size: 14px;
            pointer-events: none;
            opacity: 0.7;
        }
        button {
            background: #3C5675;
            color: #fff;
            border: 0;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            padding: 15px 30px;
            -webkit-appearance: none;
        }
        button[disabled] {
            opacity: 0.5;
        }
        #error {
            background: #D83E3E;
            color: #fff;
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .animated {
            -webkit-animation-fill-mode: both;
            animation-fill-mode: both;
            -webkit-animation-duration: 0.5s;
            animation-duration: 0.5s;
            animation-delay: 0.5s;
            -webkit-animation-delay: 0.5s;
        }
        @-webkit-keyframes fadeIn {
            0% {
                opacity: 0
            }
            100% {
                opacity: 1
            }
        }
        @keyframes fadeIn {
            0% {
                opacity: 0
            }
            100% {
                opacity: 1
            }
        }
        .fadeIn {
            -webkit-animation-name: fadeIn;
            animation-name: fadeIn;
        }
        .errors {
            margin-top: 50px;
        }
        .errors .error:first-child {
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }
        .errors .error:last-child {
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        .error {
            background: #EDF2F7;
            margin: 0 0 1px;
            padding: 20px 25px;
            text-align: left;
        }
        .error-message {
            font-size: 16px;
            color: #3C5675;
            font-weight: normal;
            margin: 0;
        }
        .error-detail {
            font-size: 13px;
            margin: 5px 0 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>{!! $logo !!}</h1>
    <div class="animated fadeIn"><h2>欢迎使用 Notadd</h2></div>
</div>
</body>
</html>