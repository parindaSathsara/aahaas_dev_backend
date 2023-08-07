<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

    {{-- <title>Document</title> --}}
</head>
<body>

    <div class="container" style="font-family:Arial,sans-serif;">
        <div class="row">
            <div class="" style="display:block;margin:0px auto;width:800px;">
                <div class="top__Layer" style="background-color: #eee;font-size:12px;padding:10px;">
                    <span>Aahaas | Seller Registration</span>
                </div>

                <img src="https://i.ibb.co/dBQHZWQ/aahaas.png" style="display:block;margin: 20px auto;" width="120px" alt="aahaas_logo" class="logo__aahaas" />

                <h4 style="color:#000;margin-top:50px;text-align:center;font-weight:bold;">Welcome to Aahaas Seller Center!</h4>

                <p style="text-align: center;margin-top:40px;font-size:19px">Your verification code is:</p>
                <p style="text-align: center;margin-top:40px;font-size:21px;font-weight:600;">{{ $otp }}</p>

                <p style="text-align: center;margin-top:40px;font-size:19px">If you haven't register as a seller or want to register as a buyer, you can <br>
                    ignore this email.</p>

                <hr/>

                <p style="text-align: center;font-size:11px">
                    You are receiving this email because you have previously registered at <u>Aahaas Seller Center</u>. Learn more about our <u>Privacy Policy</u> and <u>Terms of Use</u>.
                </p>
                <p style="text-align: center;font-size:11px">
                    Please don't reply to this email directly.
                </p>
            </div>
        </div>
    </div>
    
</body>
</html>